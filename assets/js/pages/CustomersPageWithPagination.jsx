import React, { useEffect, useState } from 'react';
import Pagination from '../components/Pagination';
import axios from 'axios';

const CustomersPageWithPagination = () => {

    const [customers, setCustomers] = useState([]);
    // const [totalItems, setTotalItems] = (0);
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 5;

    useEffect(() => {
        axios
            .get(`http://localhost:8000/api/customers?pagination=true&count=${itemsPerPage}&page=${currentPage}`
        )
        .then(response => {
            setCustomers(response.data['hydra:member']);
            setTotalItems(response.data['hydra:totalItems']);
        })
        .catch(error => console.log(error.response));
    }, [currentPage])

    const handlePageChange = (page) => {
        setCurrentPage(page);
    }

    const handleDelete = (id) => {
        const originalCustomers = [...customers];

        // 1. Approche optimiste.
        setCustomers(customers.filter(customer => customer.id !== id));

        // 2. Approche pessimiste.
        axios.delete("http://localhost:8000/api/customers/" + id)
            .then(response => console.log("ok"))
            .catch(error => {
                setCustomers(originalCustomers);
                console.log(error.response);
            });
    }

    
    const paginatedCustomers = Pagination.getData(
        customers,
        currentPage,
        itemsPerPage
    );

    return (
        <>
            <h1>La liste des clients (Pagination)</h1>
            <table className="table table-hover">
                <thead>
                    <tr>
                        <th>Id.</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Entreprise</th>
                        <th className="text-center">Factures</th>
                        <th className="text-center">Montant total</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    {customers.length === 0 && (
                        <tr>
                            <td>Chargement ...</td>
                        </tr>
                    )}
                    {paginatedCustomers.map(customer => (
                        <tr key={customer.id}>
                            <td>{customer.id}</td>
                            <td>{customer.firstName} {customer.lastName}</td>
                            <td>{customer.email}</td>
                            <td>{customer.company}</td>
                            <td className="text-center">
                                <span className="badge badge-primary">{customer.invoices.length}</span>
                            </td>
                            <td className="text-center">{customer.totalAmount.toLocaleString()} $</td>
                            <td>
                                <button
                                    onClick={() => handleDelete(customer.id)}
                                    disabled={customer.invoices.length > 0}
                                    className="btn btn-sm btn-danger"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <Pagination
                currentPage={currentPage}
                itemsPerPage={itemsPerPage}
                length={customers.length}
                onPageChanged={handlePageChange}
            />
        </>
    );
};
 
export default CustomersPageWithPagination;
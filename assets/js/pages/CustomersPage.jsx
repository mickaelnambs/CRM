import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import '../components/fontAwesome/index';
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import Pagination from '../components/Pagination';
import { default as CustomersAPI } from '../services/customersAPI';
import { toast } from "react-toastify";
import TableLoader from "../components/loaders/TableLoader";

const CustomersPage = () => {

    const [customers, setCustomers] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(true);
    const itemsPerPage = 5;

    // Permet d'aller récuperer les customers.
    const fetchCustomers = async () => {
        try {
            const data = await CustomersAPI.findAll();
            setCustomers(data);
            setLoading(false);
        } catch (error) {
            console.log(error.response)
        }
    }
    
    // Au chargement du composant, on va récuperer les customers.
    useEffect(() => {
        fetchCustomers();
    }, [])

    // Gestion du changement de page.
    const handlePageChange = (page) => {
        setCurrentPage(page);
    }

    // Gestion de la suppréssion d'un customer.
    const handleDelete = async (id) => {
        const originalCustomers = [...customers];
        setCustomers(customers.filter(customer => customer.id !== id));

        try {
            await CustomersAPI.deleteCustomer(id);
            toast.success("Le client a bien été supprimé !");
        } catch (error) {
            setCustomers(originalCustomers);
            toast.error("Une erreur est survenue !");
        }
    }

    // Gestion de la recherche.
    const handleSearch = event => {
        const value = event.currentTarget.value;
        setSearch(value);
        setCurrentPage(1);
    }

    // Filtrage des customers en fonction de la recherche.
    const filteredCustomers = customers.filter(
        c => c.firstName.toLowerCase().includes(search.toLowerCase()) ||
            c.lastName.toLowerCase().includes(search.toLowerCase()) ||
            c.email.toLowerCase().includes(search.toLowerCase()) ||
            (c.company && c.company.toLowerCase().includes(search.toLowerCase()))
    );
    
    // Pagination des données.
    const paginatedCustomers = Pagination.getData(
        filteredCustomers,
        currentPage,
        itemsPerPage
    );

    return (
        <>
            <div className="mb-3 d-flex justify-content-between align-items-center">
                <h1>La liste des clients</h1>
                <Link to="/customers/new" className="btn btn-primary"><FontAwesomeIcon icon="plus"/></Link>
            </div>
            <div className="form-group">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Rechercher ..."
                    onChange={handleSearch}
                    value={search}
                />
            </div>
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
                {!loading && (
                    <tbody>
                        {paginatedCustomers.map(customer => (
                            <tr key={customer.id}>
                                <td>{customer.id}</td>
                                <td>
                                    <Link to={"/customers/" + customer.id}>{customer.firstName} {customer.lastName}</Link>
                                </td>
                                <td>{customer.email}</td>
                                <td>{customer.company}</td>
                                <td className="text-center">
                                    <span className="badge badge-primary">{customer.invoices.length}</span>
                                </td>
                                <td className="text-center">{customer.totalAmount.toLocaleString()} €</td>
                                <td>
                                    <Link to={"/customers/" + customer.id} className="btn btn-sm btn-primary mr-2"><FontAwesomeIcon icon="edit" /></Link>
                                    <button
                                        onClick={() => handleDelete(customer.id)}
                                        // disabled={customer.invoices.length > 0}
                                        className="btn btn-sm btn-danger"
                                    >
                                        <FontAwesomeIcon icon="trash" />
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                )}
            </table>
            {loading && <TableLoader />}
            {itemsPerPage < filteredCustomers.length &&
                < Pagination
                currentPage={currentPage}
                itemsPerPage={itemsPerPage}
                length={filteredCustomers.length}
                onPageChanged={handlePageChange}
            />}
        </>
    );
};
 
export default CustomersPage;
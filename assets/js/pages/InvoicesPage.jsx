import moment from 'moment';
import React, { useEffect, useState } from 'react';
import Pagination from '../components/Pagination';
import InvoicesAPI from '../services/invoicesAPI';
import '../components/fontAwesome/index';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import TableLoader from '../components/loaders/TableLoader';

const STATUS_CLASSES = {
    PAID: "success",
    SENT: "primary",
    CANCELED: "secondary"
}

const STATUS_LABELS = {
    PAID: "Payée",
    SENT: "Envoyée",
    CANCELED: "Annulée"
}

const InvoicesPage = () => {

    const [invoices, setInvoices] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(true);
    const itemsPerPage = 5;

    // Permet d'aller récuperer les factures.
    const fetchInvoices = async () => {
        try {
            const data = await InvoicesAPI.findAll();
            setInvoices(data);
            setLoading(false);
        } catch (error) {
            console.log(error.response);
        }
    }

    // Gestion du changement de page.
    const handlePageChange = (page) => {
        setCurrentPage(page);
    }

    // S'execute après la récuperation des factures.
    useEffect(() => {
        fetchInvoices();
    }, [])

    
    // Gestion de la recherche.
    const handleSearch = (event) => {
        const value = event.currentTarget.value;
        setSearch(value);
        setCurrentPage(1);
    }

    // Gestion de la suppréssion d'une facture.
    const handleDelete = async(id) => {
        const originalsInvoices = [...invoices];
        setInvoices(invoices.filter(invoice => invoice.id !== id));

        try {
            await InvoicesAPI.deleteInvoices(id);
            toast.success("La facture a bien été supprimée !");
        } catch (error) {
            setInvoices(originalsInvoices);
            toast.error("Une erreur est survenue !");
        }
    }

    // Filtrage des invoices en fonction de la recherche.
    const filteredInvoices = invoices.filter(i => 
        i.customer.firstName.toLowerCase().includes(search.toLowerCase()) ||
        i.customer.lastName.toLowerCase().includes(search.toLowerCase()) ||
        i.amount.toString().includes(search.toLowerCase()) ||
        STATUS_LABELS[i.status].toLowerCase().includes(search.toLowerCase()) ||
        i.chrono.toString().includes(search.toLowerCase())
    );
        
    // Formater la date sous forme DD/MM/YYYY.
    const formatDate = (str) => moment(str).format('DD/MM/YYYY');
    
    // Pagination des données.
    const paginatedInvoices = Pagination.getData(
        filteredInvoices,
        currentPage,
        itemsPerPage
    );
    
    return ( 
        <>
            <div className="mb-3 d-flex justify-content-between align-items-center">
                <h1>Liste des factures</h1>
                <Link to="/invoices/new" className="btn btn-primary"><FontAwesomeIcon icon="plus" /></Link>
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
                        <th>Numéro</th>
                        <th>Client</th>
                        <th className="text-center">Date d'envoi</th>
                        <th className="text-center">Montant</th>
                        <th className="text-center">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                {!loading && (
                    <tbody>
                        {paginatedInvoices.map(invoice => 
                            <tr key={invoice.id}>
                                <td>{invoice.chrono}</td>
                                <td>
                                    <Link to={"/invoices/" + invoice.id}>
                                        {invoice.customer.firstName} {invoice.customer.lastName}
                                    </Link>
                                </td>
                                <td className="text-center">{formatDate(invoice.sentAt)}</td>
                                <td className="text-center">{invoice.amount.toLocaleString()} €</td>
                                <td className="text-center">
                                    <span
                                        className={"badge badge-" + STATUS_CLASSES[invoice.status]}
                                    >
                                        {STATUS_LABELS[invoice.status]}
                                    </span>
                                </td>
                                <td>
                                    <Link to={"/invoices/" + invoice.id} className="btn btn-sm btn-primary mr-2"><FontAwesomeIcon icon="edit" /></Link>
                                    <button
                                        className="btn btn-sm btn-danger"
                                        onClick={() => handleDelete(invoice.id)}
                                    >
                                        <FontAwesomeIcon icon="trash" />
                                    </button>
                                </td>
                            </tr>    
                        )}
                    </tbody>
                )}
            </table>
            {loading && <TableLoader />}
            {itemsPerPage < filteredInvoices.length && (
                <Pagination
                    currentPage={currentPage}
                    itemsPerPage={itemsPerPage}
                    length={filteredInvoices.length}
                    onPageChanged={handlePageChange}
                />
            )}
        </>
    );
}
 
export default InvoicesPage;
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import Field from '../components/forms/Field';
import Select from '../components/forms/Select';
import FormContentLoader from '../components/loaders/FormContentLoader';
import CustomersAPI from '../services/customersAPI';
import InvoicesAPI from '../services/invoicesAPI';

const InvoicePage = ({ history, match }) => {
    
    const { id = "new " } = match.params;

    const [invoice, setInvoice] = useState({
        amount: "",
        customer: "",
        status: "SENT"
    });

    const [customers, setCustomers] = useState([]);
    const [loading, setloading] = useState(true);

    const [editing, setEditing] = useState(false);

    const [errors, setErrors] = useState({
        amount: "",
        customer: "",
        status: ""
    });

    // Récuperation de la liste des clients.
    useEffect(() => {
        fetchCustomers();
    }, []);

    // Récuperation de la bonne facture quand l'identifiant de l'URL change.
    useEffect(() => {
        if (id !== "new") {
            setEditing(true);
            fetchInvoice(id);
        }
    }, [id]);

    // Récuperation d'une facture.
    const fetchInvoice = async id => {
        try {
            const { amount, status, customer } = await InvoicesAPI.find(id);
            // const { amount, status, customer } = data;
            setInvoice({ amount, status, customer: customer.id });
            setloading(false);
        } catch (error) {
            console.log(error.response);
        }
    }

    // Récuperation des clients.
    const fetchCustomers = async () => {
        try {
            const data = await CustomersAPI.findAll();
            setCustomers(data);
            setloading(false);
            // Si on ne modifie pas le client, 
            // çela veut dire qu'on va sélectionner le prémier client dans la liste déroulante.
            if (!invoice.customer) setInvoice({...invoice, customer: data[0].id})
        } catch (error) {
            console.log(error.response);
        }
    }

    // Gestion des changements des inputs dans le formulaire.
    const handleChange = (event) => {
        const value = event.currentTarget.value;
        const name = event.currentTarget.name;  

        setInvoice({ ...invoice, [name]: value });
    }

    // Gestion de la soumission du formulaire.
    const handleSubmit = async (event) => {
        event.preventDefault();

        try {
            if (editing) {
                await InvoicesAPI.updateInvoice(id, invoice);
                toast.success("La facture a bien été modifiée !");
            } else {
                await InvoicesAPI.createInvoice(invoice);
                toast.success("La facture a bien été créée !");
                history.replace("/invoices");
            }
        } catch (error) {
            if (error.response.data.violations) {
                const apiErros = {};
                error.response.data.violations.forEach(violation => {
                    apiErros[violation.propertyPath] = violation.message;
                });
                setErrors(apiErros);
            }
            toast.error("Une erreur est survenue !");
        }
    }

    return (  
        <>
            {editing && (<h1 className="text-center mb-4">Modifiction de la facture</h1>) || (
                <h1 className="text-center mb-4">Création d'une facture</h1>
            )}
            {loading && <FormContentLoader />}
            {!loading && (
                <div className="row">
                    <div className="col-md-3"></div>
                    <div className="col-md-6">
                        <form onSubmit={handleSubmit}>
                            <Field
                                name="amount"
                                type="number"
                                placeholder="Montant de la facture"
                                label="Montant"
                                onChange={handleChange}
                                value={invoice.amount}
                                error={errors.amount}
                            />
                            <Select
                                name="customer"
                                label="Client"
                                value={invoice.customer}
                                error={errors.customer}
                                onChange={handleChange}
                            >
                                {customers.map(customer => 
                                    <option key={customer.id} value={customer.id}>
                                        {customer.firstName} {customer.lastName}
                                    </option>    
                                )}
                            </Select>
                            <Select
                                name="status"
                                label="Status"
                                value={invoice.status}
                                error={errors.status}
                                onChange={handleChange}
                            >
                                <option value="SENT">Envoyée</option>
                                <option value="PAID">Payée</option>
                                <option value="CANCELED">Annulée</option>
                            </Select>
                            <div className="form-group">
                                <button type="submit" className="btn btn-success mr-2">Enregister</button>
                                <Link to="/invoices" className="btn btn-primary">Retour</Link>
                            </div>
                        </form>
                    </div>
                    <div className="col-md-3"></div>
                </div>
            )}
        </>
    );
}
 
export default InvoicePage;
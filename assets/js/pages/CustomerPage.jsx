import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import Field from '../components/forms/Field';
import FormContentLoader from '../components/loaders/FormContentLoader';
import CustomersAPI from '../services/customersAPI';

const CustomerPage = ({match, history}) => {

    const { id = "new" } = match.params;

    const [customer, setCustomer] = useState({
        lastName: "",
        firstName: "",
        email: "",
        company: ""
    });

    const [errors, setErrors] = useState({
        lastName: "",
        firstName: "",
        email: "",
        company: ""
    });

    const [editing, setEditing] = useState(false);
    const [loading, setLoading] = useState(false);

    // Récuperation du customer en fonction de l'identifiant.
    const fetchCustomer = async (id) => {
        try {
            // const data = await CustomersAPI.find(id);
            // const { firstName, lastName, email, company } = data;
            const { firstName, lastName, email, company } = await CustomersAPI.find(id);
            setCustomer({ firstName, lastName, email, company });
            setLoading(false);
        } catch (error) {
            console.log(error.response);
            history.replace("/customers");
        }
    }

    // Chargement du customer si besoin au chargement 
    // du composant ou au changement de l'identifiant.
    useEffect(() => {
        if (id !== "new") {
            setEditing(true);
            fetchCustomer(id);
            setLoading(true);
        }
    }, [id])

    // Gestion des changements des inputs dans le formulaire.
    const handleChange = (event) => {
        const value = event.currentTarget.value;
        const name = event.currentTarget.name;

        setCustomer({ ...customer, [name]: value });
    }

    // Gestion de la soumission du formulaire.
    const handleSubmit = async event => {
        event.preventDefault();

        try {
            if (editing) {
                await CustomersAPI.updateCustomer(id, customer);
                history.replace("/customers");
                toast.success("Le client a bien été modifié !");
            } else {
                await CustomersAPI.createCustomer(customer);
                toast.success("Le client a bien été crée !");
                history.replace("/customers");
            }
            setErrors({});
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
            {(!editing && <h1 className="text-center mb-4">Création d'un client</h1>) || (
                <h1 className="text-center mb-4">Modification du client</h1>
            )}
            {!loading && <div className="row">
                <div className="col-md-3"></div>
                <div className="col-md-6">
                    <form onSubmit={handleSubmit}>
                        <Field
                            name="lastName"
                            label="Nom de famille"
                            placeholder="Nom de famille du client ..."
                            value={customer.lastName}
                            onChange={handleChange}
                            error={errors.lastName}
                        />
                        <Field
                            name="firstName"
                            label="Prénom(s)"
                            placeholder="Prénom(s) du client ..."
                            value={customer.firstName}
                            onChange={handleChange}
                            error={errors.firstName}
                        />
                        <Field
                            name="email"
                            label="Email"
                            placeholder="Adresse email du client ..."
                            type="email"
                            value={customer.email}
                            onChange={handleChange}
                            error={errors.email}
                        />
                        <Field
                            name="company"
                            label="Entreprise"
                            placeholder="Entreprise du client ..."
                            value={customer.company}
                            onChange={handleChange}
                            error={errors.company}
                        />
                        <div className="form-group">
                            <button type="submit" className="btn btn-success mr-2">Enregister</button>
                            <Link to="/customers" className="btn btn-primary">Retour</Link>
                        </div>
                    </form>
                </div>
                <div className="col-md-3"></div>
            </div>}
            {loading && (<FormContentLoader />)}
        </>
    );
}
 
export default CustomerPage;
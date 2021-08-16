import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import Field from '../components/forms/Field';
import UsersAPI from '../services/usersAPI';

const RegisterPage = ({history}) => {
    const [user, setUser] = useState({
        firstName: "",
        lastName: "",
        email: "",
        password: "",
        confirmPassword: ""
    });

    const [errors, setErrors] = useState({
        firstName: "",
        lastName: "",
        email: "",
        password: "",
        confirmPassword: ""
    });

    // Gestion des changements des inputs dans le formulaire.
    const handleChange = ({ currentTarget }) => {
        const { name, value } = currentTarget;
        setUser({ ...user, [name]: value });
    }

    // Gestion de la soumission du formulaire.
    const handleSubmit = async event => {
        event.preventDefault();
        
        try {
            await UsersAPI.register(user);
            toast.success("Vous êtes désormais inscrit !");
            history.replace("/login");
        } catch (error) {   
            const { violations } = error.response.data;
            if (violations) {
                const apiErrors = {};
                violations.forEach(violation => {
                    apiErrors[violation.propertyPath] = violation.message;
                });
                setErrors(apiErrors);
            }
            toast.error("Une erreur est survenue !");
        }
    }

    return (  
        <>
            <h1 className="text-center">Inscription</h1>
            <div className="row">
                <div className="col-md-3"></div>
                <div className="col-md-6">
                    <form onSubmit={handleSubmit}>
                        <Field
                            name="lastName"
                            type="text"
                            placeholder="Votre nom de famille ..."
                            label="Nom de famille"
                            onChange={handleChange}
                            value={user.lastName}
                            error={errors.lastName}
                        />
                        <Field
                            name="firstName"
                            type="text"
                            placeholder="Votre prénom ..."
                            label="Prénom(s)"
                            onChange={handleChange}
                            value={user.firstName}
                            error={errors.firstName}
                        />
                        <Field
                            name="email"
                            type="email"
                            placeholder="Votre adresse email ..."
                            label="Adresse Email"
                            onChange={handleChange}
                            value={user.email}
                            error={errors.email}
                        />
                        <Field
                            name="password"
                            type="password"
                            placeholder="Votre mot de passe ..."
                            label="Mot de passe"
                            onChange={handleChange}
                            value={user.password}
                            error={errors.password}
                        />
                        <Field
                            name="confirmPassword"
                            type="password"
                            placeholder="Confirmation de mot de passe ..."
                            label="Confirmez votre mot de passe"
                            onChange={handleChange}
                            value={user.confirmPassword}
                            error={errors.confirmPassword}
                        />
                        <div className="form-group">
                            <button type="submit" className="btn btn-success mr-2">S'inscrire</button>
                            <Link to="/login" className="btn btn-link">J'ai déjà un compte</Link>
                        </div>
                    </form>
                </div>
                <div className="col-md-3"></div>
            </div>
        </>
    );
}
 
export default RegisterPage;
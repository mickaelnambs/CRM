import React, { useState, useContext } from 'react';
import AuthAPI from '../services/authAPI';
import AuthContext from '../contexts/AuthContext';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Field from '../components/forms/Field';
import { toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.min.css';

const LoginPage = ({ history}) => {

    const [credentials, setCredentials] = useState({
        email: "",
        password: ""    
    });

    const { setIsAuthenticated } = useContext(AuthContext);

    const [error, setError] = useState("");
    
    // Soumission du submit.
    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            await AuthAPI.authenticate(credentials);
            setError("");
            setIsAuthenticated(true);
            toast.success("Vous êtes maintenant connecté 😎!");
            history.replace("/customers");
        } catch (error) {
            console.log(error.response);
            setError("Les informations que vous avez fournies ne sont pas bonnes.");
            toast.error("Une erreur est survenue !");
        }
    }

    // Gestion des champs.
    const handleChange = event => {
        const value = event.currentTarget.value;
        const name = event.currentTarget.name;

        setCredentials({ ...credentials, [name]: value });
    };


    return ( 
        <>
            <div className="row my-5">
                <div className="col-md-3"></div>
                <div className="col-md-5">
                    <div className="card bg-light">
                        <div className="card-header bg-primary text-white">
                            <h4 className="card-title text-center"><FontAwesomeIcon icon="lock" /> Connectez-vous</h4>
                        </div>
                        <div className="card-body">
                            <form onSubmit={handleSubmit}>
                                <Field
                                    label="Adresse email"
                                    value={credentials.email}
                                    type="email"
                                    name="email"
                                    onChange={handleChange}
                                    error={error}
                                    placeholder="Adresse email ..."
                                />
                                <Field
                                    label="Mot de passe"
                                    value={credentials.password}
                                    type="password"
                                    name="password"
                                    onChange={handleChange}
                                    error={error}
                                    placeholder="Mot de passe ..."
                                />
                                <div className="form-group">
                                    <button type="submit" className="btn btn-primary">
                                        Connexion
                                    </button>
							    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
 
export default LoginPage;
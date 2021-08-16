import React, { useContext } from 'react';
import { NavLink } from 'react-router-dom';
import AuthAPI from '../services/authAPI';
import './fontAwesome/index';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import AuthContext from '../contexts/AuthContext';
import { toast } from 'react-toastify';

const Navbar = ({history}) => {

    const { isAuthenticated, setIsAuthenticated } = useContext(AuthContext);

    const handleLogout = () => {
        AuthAPI.logout();
        setIsAuthenticated(false);
        toast.info("Vous êtes désormais déconnecté");
        history.push("/login");
    }

    return (  
        <nav className="navbar navbar-expand-lg navbar-dark bg-primary">
            <NavLink to="/" className="navbar-brand"><b>CRM</b></NavLink>
            <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span className="navbar-toggler-icon"></span>
            </button>

            <div className="collapse navbar-collapse" id="navbarColor01">
                <ul className="navbar-nav mr-auto">
                    <li className="nav-item">
                        <NavLink to="/customers" className="nav-link"><FontAwesomeIcon icon="users" /> clients</NavLink>
                    </li>
                    <li className="nav-item">
                        <NavLink to="/invoices" className="nav-link"><FontAwesomeIcon icon="file-invoice" /> factures</NavLink>
                    </li>
                </ul>
                <ul className="navbar-nav ml-auto">
                    {!isAuthenticated && (
                        <>
                            <li className="nav-item">
                                <NavLink className="nav-link" to="/register"><FontAwesomeIcon icon="sign-in-alt" /> inscription</NavLink>
                            </li>
                            <li className="nav-item">
                                <NavLink to="/login" className="nav-link"><FontAwesomeIcon icon="lock" /> connexion</NavLink>
                            </li>
                        </>
                    ) || (
                        <button
                            onClick={handleLogout}
                            className="btn btn-danger"
                        >
                            Déconnexion
                        </button>
                    )}
                </ul>
            </div>
        </nav>
    );
}
 
export default Navbar;
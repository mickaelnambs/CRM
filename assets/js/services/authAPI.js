import axios from 'axios';
import jwtDecode from 'jwt-decode';
import { LOGIN_API } from '../config';

/**
 * Déconnexion (suppréssion du token dans le localStorage et sur Axios).
 */
function logout() {
    window.localStorage.removeItem("authToken");
    delete axios.defaults.headers["authorization"];
}

/**
 * 
 * Requête HTTP d'authentification et stockage du token dans le storage et sur Axios.
 * 
 * @param {object} credentials 
 */
function authenticate(credentials) {
    return axios
        .post(LOGIN_API, credentials)
        .then(response => response.data.token)
        .then(token => {
            // Je stocke le token dans mon local storage.
            window.localStorage.setItem("authToken", token);
            // On prévient axios qu'on a maintenant un header par défaut
            // sur toutes nos futurs requestes.
            setAxiosToken(token);
        });
}

/**
 * Positionne le token jwt sur axios.
 * 
 * @param {string} token le token JWT
 */
function setAxiosToken(token) {
    axios.defaults.headers["Authorization"] = "Bearer " + token;
}

/**
 * Mise en place lors du chargement de l'application.
 */
function setup() {
    // Voir si on a le token.
    const token = window.localStorage.getItem("authToken");

    // Si le token est encore valide. micknj
    if (token) {
        const jwtData = jwtDecode(token);
        if (jwtData.exp * 1000 > new Date().getTime()) {
            setAxiosToken(token);
        }
    }
}

/**
 * Permet de savoir si on est authentifié ou pas.
 * @returns boolean
 */
function isAuthenticated() {
    // Voir si on a le token.
    const token = window.localStorage.getItem("authToken");

    // Si le token est encore valide. micknj
    if (token) {
        const jwtData = jwtDecode(token);
        if (jwtData.exp * 1000 > new Date().getTime()) {
            return true;
        }
        return false;
    }
    return false;
}

export default {
    authenticate,
    logout,
    setup,
    isAuthenticated
}
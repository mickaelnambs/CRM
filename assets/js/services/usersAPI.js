import axios from 'axios';
import { USERS_API } from '../config';

// Permet à l'user de s'inscrire.
function register(user) {
    return axios.post(USERS_API, user);
}

export default {
    register
}
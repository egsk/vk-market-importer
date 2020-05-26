import axios from 'axios';

function deleteEntity(id) {
    return axios.delete(`/api/import-target/${id}`);
}

export default {
    deleteEntity
}
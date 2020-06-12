import axios from 'axios';

function getUploadTask(id) {
    return axios.get(`/api/upload-task/${id}`);
}

export default {
    getUploadTask
}
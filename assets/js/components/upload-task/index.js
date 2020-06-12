import uploadTaskController from "../../controllers/uploadTaskController";

(function () {
    const root = $('#upload-result-holder');
    const statusDictionary = {
        'created': 'Создан',
        'failed_to_create': 'Не удалось создать',
        'updated': 'Обновлён',
        'failed_to_update': 'Не удалось обновить',
        'deleted': 'Удалён',
        'failed_to_delete': 'Не удалось удалить'
    }
    if (!root) {
        return;
    }
    const id = root.data('id');
    const h1 = $('h1');
    uploadTaskController.getUploadTask(id).then((res) => {
        handleUploadTask(res.data);
    })
    const listHolder = $('<ul>', {class: 'list-group'})
    listHolder.appendTo(root)
    const interval = window.setInterval(
        () => {
            uploadTaskController.getUploadTask(id).then((res) => {
                handleUploadTask(res.data);
            })
        }, 1000);

    function handleUploadTask(uploadTask) {
        switch (uploadTask.status) {
            case 'finished':
                handleFinishedTask(uploadTask)
                break;
            case 'in_progress':
                handleTaskInProgress(uploadTask)
                break;
            case 'new':
            default:
                handleTaskInQueue(uploadTask)
        }
    }

    function handleFinishedTask(uploadTask) {
        h1.text('Загрузка завершена');
        window.clearInterval(interval);
        renderUploadedProducts(uploadTask.uploadedProducts)
    }
    function handleTaskInQueue(uploadTask) {
        h1.text('В очереди')
    }
    function handleTaskInProgress(uploadTask) {
        h1.text('Загружается...');
        renderUploadedProducts(uploadTask.uploadedProducts)
    }

    function renderUploadedProducts(uploadedProducts) {
        listHolder.empty();
        uploadedProducts.forEach(el => {
            console.log(el);
            const text = `Имя: ${el.name}; Id в источнике: ${el.sourceId}; Статус: ${statusDictionary[el.status]}`
            const item = $('<li>', {class: 'list-group-item', text})
            item.appendTo(listHolder);
        })

    }
})()


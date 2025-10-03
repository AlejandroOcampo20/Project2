// Espera a que el DOM esté cargado antes de ejecutar la lógica
document.addEventListener('DOMContentLoaded', () => {

    // Endpoint de la API
    const API_URL = 'index.php?url=users';

    // Referencias a elementos del DOM
    const userForm = document.getElementById('user-form');
    const userIdInput = document.getElementById('user-id');
    const documentInput = document.getElementById('document');
    const nameInput = document.getElementById('name');
    const ageInput = document.getElementById('age');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const userTableBody = document.getElementById('user-table-body');
    const btnClear = document.getElementById('btn-clear');

    /**
     * GET: Obtener todos los usuarios y renderizarlos en la tabla
     */
    const fetchUsers = async () => {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al obtener los usuarios');
            }

            const users = result.data;
            userTableBody.innerHTML = '';

            if (users && users.length > 0) {
                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.document}</td>
                        <td>${user.name}</td>
                        <td>${user.created_at}</td>
                        <td>
                            <button class="btn btn-info btn-sm btn-details" data-id="${user.id}">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btn-edit" data-id="${user.id}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="${user.id}">
                                <i class="fa-solid fa-trash"></i>
                            </button>

                        </td>
                    `;
                    userTableBody.appendChild(row);
                });
            } else {
                userTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron usuarios.</td></tr>';
            }
        } catch (error) {
            console.error('Error en fetchUsers:', error);
            userTableBody.innerHTML = `<tr><td colspan="5" class="text-center">${error.message}</td></tr>`;
        }
    };

    /**
     * POST/PUT: Crear o actualizar un usuario desde el formulario
     */
    const handleFormSubmit = async (e) => {
        e.preventDefault();

        const userData = {
            document: documentInput.value,
            name: nameInput.value,
            age: ageInput.value,
            phone: phoneInput.value,
            address: addressInput.value
        };

        const userId = userIdInput.value;
        const method = userId ? 'PUT' : 'POST';
        const url = userId ? `${API_URL}/${userId}` : API_URL;

        try {
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Error en la solicitud');
            }

            alert(result.message);
            clearForm();
            fetchUsers();

        } catch (error) {
            console.error('Error en handleFormSubmit:', error);
            alert(`Error: ${error.message}`);
        }
    };

    /**
     * GET: Cargar datos de un usuario en el formulario para edición
     */
    const handleEdit = async (id) => {
        try {
            const response = await fetch(`${API_URL}/${id}`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Usuario no encontrado');
            }

            userIdInput.value = data.id;
            documentInput.value = data.document;
            nameInput.value = data.name;
            ageInput.value = data.age;
            phoneInput.value = data.phone;
            addressInput.value = data.address;

        } catch (error) {
            console.error('Error en handleEdit:', error);
            alert(error.message);
        }
    };

    /**
     * DELETE: Eliminar un usuario
     */
    const handleDelete = async (id) => {
        if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) return;

        try {
            const response = await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error al eliminar el usuario');
            }

            alert(result.message);
            fetchUsers();

        } catch (error) {
            console.error('Error en handleDelete:', error);
            alert(error.message);
        }
    };

    /**
     * GET: Mostrar detalles de un usuario en un modal Bootstrap
     */
    const handleDetails = async (id) => {
        try {
            const response = await fetch(`${API_URL}/${id}`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Usuario no encontrado');
            }

            const detailsList = document.getElementById('user-details-list');
            detailsList.innerHTML = `
                <li class="list-group-item"><strong>ID:</strong> ${data.id}</li>
                <li class="list-group-item"><strong>Documento:</strong> ${data.document}</li>
                <li class="list-group-item"><strong>Nombre:</strong> ${data.name}</li>
                <li class="list-group-item"><strong>Edad:</strong> ${data.age}</li>
                <li class="list-group-item"><strong>Teléfono:</strong> ${data.phone}</li>
                <li class="list-group-item"><strong>Dirección:</strong> ${data.address}</li>
                <li class="list-group-item"><strong>Creado:</strong> ${data.created_at}</li>
            `;

            const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
            modal.show();

        } catch (error) {
            console.error('Error en handleDetails:', error);
            alert(error.message);
        }
    };

    /**
     * Resetear el formulario a su estado inicial
     */
    const clearForm = () => {
        userForm.reset();
        userIdInput.value = '';
    };

    // --- EVENT LISTENERS ---

    // Cargar usuarios al inicio
    fetchUsers();

    // Guardar usuario (crear/editar)
    userForm.addEventListener('submit', handleFormSubmit);

    // Limpiar formulario
    btnClear.addEventListener('click', clearForm);

    // Delegación de eventos para los botones de acción en la tabla
    userTableBody.addEventListener('click', (e) => {
        const target = e.target;
        const id = target.getAttribute('data-id');

        if (target.classList.contains('btn-edit')) {
            handleEdit(id);
        } else if (target.classList.contains('btn-delete')) {
            handleDelete(id);
        } else if (target.classList.contains('btn-details')) {
            handleDetails(id);
        }
    });
});

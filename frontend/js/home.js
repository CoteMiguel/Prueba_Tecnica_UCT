document.addEventListener("DOMContentLoaded", async function () {
    session();
    cargarTiposSolicitud();
    openModal();
    logout();
    addSolicitud();
    cargarSolicitudes();
});

function aplicarPermisos(permisos) {
    const btnNueva = document.getElementById("btnNuevaSolicitud");
    if (permisos.crear === "1") {
        btnNueva.style.display = "inline-block";
    } else {
        btnNueva.style.display = "none";
    }
    window.permisosUsuario = permisos;
    // editar = 1 y borrar = 0 muestra solo si cuando ambos sean 0 no muestra nada de acciones
    const colAcciones = document.getElementById("colAcciones");
    const estadoSelect = document.getElementById("estado");
    estadoSelect.innerHTML = "";
    if (permisos.editar === "0" && permisos.eliminar === "0") {
        colAcciones.style.display = "none";
        const option = document.createElement("option");
        option.value = "Pendiente";
        option.textContent = "Pendiente";
        estadoSelect.appendChild(option);
    } else {
        colAcciones.style.display = "table-cell";
        ["Pendiente", "En revisión", "Aprobada", "Rechazada"].forEach(op => {
            const option = document.createElement("option");
            option.value = op;
            option.textContent = op;
            estadoSelect.appendChild(option);
        });
    }
}


async function cargarTiposSolicitud() {
    try {
        const response = await fetch("../backend/index.php?action=listarTipos");
        const data = await response.json();

        // llenar buscador
        const select = document.getElementById("filtroTipo");
        select.innerHTML = ``;
        data.forEach(tipo => {
            const option = document.createElement("option");
            option.value = tipo.id_tiso;
            option.textContent = tipo.nombre;
            select.appendChild(option);
        });
        // llenar modal
        const filtroModal = document.getElementById("TipoModal");
        filtroModal.innerHTML = ``;
        data.forEach(tipo => {
            const option = document.createElement("option");
            option.value = tipo.id_tiso;
            option.textContent = tipo.nombre;
            filtroModal.appendChild(option);
        });
    } catch (error) {
        console.error("Error cargando tipos:", error);
    }
}

async function session() {
    try {
        const response = await fetch("../backend/session.php");
        const data = await response.json();
        if (data && data.nombre) {
            console.log("Usuario en sesión:", data);
            document.getElementById("solicitante").value = data.nombre;
            document.getElementById("solicitanteId").value = data.id;
        }
        aplicarPermisos(data.permisos);

    } catch (error) {
        console.error("Error cargando tipos:", error);
    }
}

function openModal() {
    const btnNuevaSolicitud = document.getElementById("btnNuevaSolicitud");
    const modalNuevaSolicitud = new bootstrap.Modal(document.getElementById("modalNuevaSolicitud"));
    btnNuevaSolicitud.addEventListener("click", () => {
        document.getElementById("solicitanteId").value = ""; // nuevo
        document.getElementById("formNuevaSolicitud").reset(); // limpiar campos
        modalNuevaSolicitud.show();
    });

}

function logout() {
    document.getElementById('btnLogout').addEventListener('click', async function () {
        const response = await fetch('../backend/index.php?action=logout');
        const data = await response.json();
        if (data.success) {
            window.location.href = 'index.html';
        }
    });
}
async function addSolicitud() {
    try {
        const form = document.getElementById("formNuevaSolicitud");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const idSolicitud = document.getElementById("solicitanteId").value;
            const datos = {
                id_solicitud: idSolicitud,
                solicitanteId: document.getElementById("solicitanteId").value,
                tipo: document.getElementById("TipoModal").value,
                descripcion: document.getElementById("descripcion").value,
                estado: document.getElementById("estado").value
            };
            const action = idSolicitud ? "actualizarSolicitud" : "crearSolicitud";

            const response = await fetch(`../backend/index.php?action=${action}`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });
            const data = await response.json();
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById("modalNuevaSolicitud"));
                modal.hide();
                cargarSolicitudes();
            } else {
                alert("Error: " + data.msg);
            }
        });
    } catch (error) {
        console.error("Error guardando solicitud:", error);
    }
}
async function cargarSolicitudes() {
    try {
        const response = await fetch("../backend/index.php?action=listarSolicitudes");
        const data = await response.json();
        console.log(data);
        const tabla = document.getElementById("tablaSolicitudes");
        tabla.innerHTML = "";

        if (data.length === 0) {
            tabla.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No hay solicitudes registradas</td></tr>`;
            return;
        }

        data.forEach(solicitud => {
            let acciones = "";
            if (window.permisosUsuario.editar === "1") {
                acciones += `
                    <button 
                        class="btn btn-sm btn-outline-primary btnEditar" 
                        data-id="${solicitud.id_solicitud}" 
                        data-solicitante="${solicitud.solicitante}" 
                        data-tipo="${solicitud.tipo}" 
                        data-id-tipo="${solicitud.id_tipo}" 
                        data-descripcion="${solicitud.descripcion}" 
                        data-estado="${solicitud.estado}">
                            <i class="bi bi-pencil"></i>
                    </button>`;
            }
            if (window.permisosUsuario.eliminar === "1") {
                acciones += `<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>`;
            }
            const fila = `
                <tr>
                <td>${solicitud.id_solicitud}</td>
                <td>${solicitud.solicitante}</td>
                <td>${solicitud.tipo}</td>
                <td>${solicitud.descripcion}</td>
                <td>${solicitud.estado}</td>
                <td>${solicitud.fecha_creacion}</td>
                <td class="text-center">${acciones}</td>
                </tr>
            `;
            tabla.insertAdjacentHTML("beforeend", fila);
        });
        activarBotonesEditar();
    } catch (error) {
        console.error("Error cargando solicitudes:", error);
    }
}
function activarBotonesEditar() {
    document.querySelectorAll(".btnEditar").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-id");
            const solicitante = btn.getAttribute("data-solicitante");
            const tipo = btn.getAttribute("data-tipo");
            const id_tipo = btn.getAttribute("data-id-tipo");
            const descripcion = btn.getAttribute("data-descripcion");
            const estado = btn.getAttribute("data-estado");

            document.getElementById("solicitante").value = solicitante;
            document.getElementById("solicitanteId").value = id;
            document.getElementById("TipoModal").value = id_tipo;
            document.getElementById("descripcion").value = descripcion;
            document.getElementById("estado").value = estado;

            // abrir modal
            const modal = new bootstrap.Modal(document.getElementById("modalNuevaSolicitud"));
            modal.show();
        });
    });
}

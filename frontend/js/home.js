document.addEventListener("DOMContentLoaded", async function () {
    session();
    cargarTiposSolicitud();
    openModal();
    logout();
    addSolicitud();

});

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

    } catch (error) {
        console.error("Error cargando tipos:", error);
    }
}

function openModal() {
    const btnNuevaSolicitud = document.getElementById("btnNuevaSolicitud");
    const modalNuevaSolicitud = new bootstrap.Modal(document.getElementById("modalNuevaSolicitud"));
    btnNuevaSolicitud.addEventListener("click", () => {
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

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            const datos = {
                solicitanteId: document.getElementById("solicitanteId").value,
                solicitante: document.getElementById("solicitante").value,
                tipo: document.getElementById("TipoModal").value,
                descripcion: document.getElementById("descripcion").value,
                estado: document.getElementById("estado").value
            };

            fetch("../backend/index.php?action=crearSolicitud", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            })
                .then(res => res.json())
                .then(data => {
                    console.log("Respuesta del backend:", data);
                })
                .catch(err => console.error("Error guardando solicitud:", err));
        });
    } catch (error) {
        console.error("Error cargando tipos:", error);
    }
}

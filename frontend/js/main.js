document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const correo = document.getElementById("email").value;
        const passwd = document.getElementById("passwd").value;

        const response = await fetch("../backend/index.php?action=login", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `correo=${encodeURIComponent(correo)}&passwd=${encodeURIComponent(passwd)}`
        });

        const text = await response.text();  
        try {
            const data = JSON.parse(text);
            if (data.success) {
                window.location.href = "home.html";
            } else {
                alert(data.msg || "Error en login");
            }
        } catch (e) {
            alert("Error del servidor (revisa consola)");
        }



    })
});
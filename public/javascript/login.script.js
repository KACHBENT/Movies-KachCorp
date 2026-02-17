  (function () {
    const $ = (sel) => document.querySelector(sel);
    const pass = $("#passInput");
    const btn  = $("#btnToggle");
    const icon = $("#toggleIcon");

    if (btn && pass && icon) {
      btn.addEventListener("click", () => {
        const showing = pass.type === "text";
        pass.type = showing ? "password" : "text";
        const viewIcon = btn.dataset.iconView;
        const hideIcon = btn.dataset.iconHide;
        icon.src = showing ? hideIcon : viewIcon;
        icon.alt = showing ? "Ocultar" : "Mostrar";
        btn.setAttribute("aria-pressed", String(!showing));
      });
    }

    const form = $("#formLogin");
    if (form) {
      form.addEventListener("submit", (e) => {
        if (!form.checkValidity()) {
          e.preventDefault();
          form.classList.add("was-validated");
        }
      });
    }
  })();
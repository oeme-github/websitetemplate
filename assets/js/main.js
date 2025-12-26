// ==============================================
// --- Menu ---
function toggleMenu() {
  const menu = document.getElementById('mobileMenu');
  if (menu) {
    menu.classList.toggle('active');
  }
}

// ==============================================
// --- Count-up Animation ---
const counters = document.querySelectorAll('.count');
let counterStarted = false;

function startCounters() {
  if (counterStarted) return;

  counters.forEach(counter => {
    const target = Number(counter.dataset.target);
    let current = 0;
    const increment = target / 80;

    function updateCounter() {
      current += increment;
      if (current < target) {
        counter.textContent = Math.ceil(current);
        requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = target + '+';
      }
    }

    updateCounter();
  });

  counterStarted = true;
}

// ==============================================
// scroll
window.addEventListener('scroll', () => {
  const section = document.getElementById('zahlen');
  if (!section) return;

  if (section.getBoundingClientRect().top < window.innerHeight * 0.8) {
    startCounters();
  }
});


// ==============================================
// --- EVENT LISTENER ---
document.addEventListener('DOMContentLoaded', () => {
  const burger = document.querySelector('.burger');
  const menu = document.getElementById('mobileMenu');

  if (burger && menu) {
    burger.addEventListener('click', () => {
      menu.classList.toggle('active');
    });
  }

  document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => {
      menu.classList.remove('active');
    });
  });
});

// ==============================================
// submitBtn Button handling
function setButtonLoading(button, text = "Sende...") {
  button.disabled = true;
  button.textContent = text;
}

function resetButton(button, text = "SEPA-Mandat absenden") {
  button.disabled = false;
  button.textContent = text;
}

// ==============================================
// Fetch-Logik submitBtn
async function submitForm(url, formData) {
  const response = await fetch(url, {
    method: "POST",
    body: formData
  });

  const contentType = response.headers.get("content-type") || "";

  console.debug("-> submit", response.status);
  const clone = response.clone();
  console.debug(await clone.text());
// ✅ Die korrekte Regel (wichtig!)
// Status	Bedeutung	Behandlung
// 200	OK	response.json()
// 204	Honeypot	still abbrechen
// 422	Validierung	JSON lesen & anzeigen
// 500	Serverfehler	in catch

  // 👉 Honeypot
  if (response.status === 204) {
    return null;
  }

  // 👉 VALIDIERUNGSFEHLER (wichtig!)
  if (response.status === 422 && contentType.includes("application/json")) {
    return await response.json(); // success: false, message / errors
  }

  // JSON erwartet, aber HTML bekommen → PHP-Fehler
  if (!contentType.includes("application/json")) {
    const text = await response.text();
    throw new Error("PHP-Fehler:\n" + text);
  }

  // 👉 echte Serverfehler
  if (!response.ok) {
    const text = await response.text();
    throw new Error(text || "Serverfehler");
  }

   // ✅ Erfolg 
  return await response.json();
}

  
// ==============================================
// error message 
function showFormMessage(message, type = "error") {
  const box = document.getElementById("formMessage");
  if (!box) return;

  box.textContent = message;
  box.className = `form-message ${type}`;
}

function showValidationErrors(errors) {
  const box = document.getElementById("formMessage");
  if (!box) return;

  // Sammelbox
  const list = document.createElement("ul");
  list.style.margin = "0";
  list.style.paddingLeft = "1.2rem";

  box.innerHTML = "";
  box.className = "form-message error";

  // Alte Feldmarkierungen entfernen
  document.querySelectorAll(".field-error")
    .forEach(el => el.classList.remove("formMessage"));

  // Fehler durchgehen
  Object.entries(errors).forEach(([field, message]) => {
    // Liste
    const li = document.createElement("li");
    li.textContent = message;
    list.appendChild(li);

    // Feld markieren
    const input = document.querySelector(`[name="${field}"]`);
    if (input) {
      input.classList.add("field-error");
    }
  });

  box.appendChild(list);
}

function focusFirstError(errors) {
  const firstFieldName = Object.keys(errors)[0];
  const field = document.querySelector(`[name="${firstFieldName}"]`);
  if (field) field.focus();
}

// ==============================================
// Meldungen löschen
function clearFormMessage() {
  const box = document.getElementById("formMessage");
  if (box) {
    box.innerHTML = "";
    box.className = "form-message";
  }
}

// ==============================================
// SEPA-Button submit submitBtn
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("kontaktForm");
  // check if form exists
  if (!form) return;
  // get submit button
  const button = document.getElementById("submitBtn");
  submitBtn.disabled = true;

  // CSRF-Token holen
  fetch('PHP/csrf.php', {
      credentials: 'same-origin'
  })
  .then(res => res.json())
  .then(data => {
      const tokenField = document.getElementById('csrf_token');
      if (tokenField && data.csrf_token) {
          tokenField.value = data.csrf_token;
          submitBtn.disabled = false;
      }
  })
  .catch(() => {
      console.error('CSRF-Token konnte nicht geladen werden');
  });  
  // =========================
  // Submit-Handler
  // =========================
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    // init 
    clearFormMessage();
    setButtonLoading(button);

    try {
      const result = await submitForm("PHP/send.php", new FormData(form));

      console.error(result);  

      if (!result || result.spam) {
        resetButton(button);
        return;
      }

      if (result.errors) {
        showValidationErrors(result.errors);
        focusFirstError(result.errors);
        resetButton(button);
        return;
      }

    if (result && result.success) {
        showFormMessage("Erfolgreich gesendet!", "success");
        window.location.href = "danke.html";
        resetButton(button);
        return;
      }


    } catch (error) {
      console.error(error);
      showFormMessage("Technischer Fehler. Bitte später erneut versuchen.");
      resetButton(button);
    }
  });
  // =========================
  // UX-Bonus: Fehler beim Tippen entfernen
  // =========================
  form.addEventListener("input", (e) => {
    e.target.classList.remove("field-error");
  });

});

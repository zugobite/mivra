/**
 * Initializes AJAX-based contact form submission for improved user experience.
 *
 * This script waits for the DOM to be ready, then attaches a submit event handler
 * to the contact form. The form is submitted asynchronously using Fetch API, and
 * the server's response is displayed in a result container without reloading the page.
 *
 * Progressive enhancement: If Fetch API is unavailable, the form will submit normally.
 *
 * Expected server JSON response format:
 * ```json
 * {
 *   "ok": true // or false
 *   // Optional: "errors": { "fieldName": "Error message" }
 * }
 * ```
 *
 * HTML structure requirements:
 * - A form element with `id="contact-form"` and a valid `action` attribute.
 * - A result container element with `id="contact-result"`.
 */
document.addEventListener("DOMContentLoaded", () => {
  /** @type {HTMLFormElement|null} The contact form element */
  const form = document.getElementById("contact-form");

  /** @type {HTMLElement|null} The result message container element */
  const result = document.getElementById("contact-result");

  // Abort if required elements are missing
  if (!form || !result) return;

  /**
   * Handles form submission using Fetch API.
   *
   * @param {SubmitEvent} e - The form submit event.
   * @returns {Promise<void>} Resolves when submission is complete.
   */
  form.addEventListener("submit", async (e) => {
    if (!window.fetch) return; // progressive enhancement: fall back to normal submit if fetch is unsupported
    e.preventDefault();

    // Prepare form data for sending
    const fd = new FormData(form);

    // Send request via Fetch API
    const resp = await fetch(form.action, { method: "POST", body: fd });

    /** @type {string} Message to display in result container */
    let text = "";

    try {
      const json = await resp.json();

      if (json.ok) {
        text = "Thank you! Your message was sent.";
      } else if (json.errors) {
        // Combine all error messages into a single string
        text = Object.values(json.errors).join(" ");
      } else {
        text = "Unexpected response.";
      }
    } catch {
      text = "Unable to submit right now.";
    }

    // Display result message
    result.textContent = text;
  });
});

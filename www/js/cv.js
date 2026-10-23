// "Descarcă PDF": opens the browser's print dialog.
// The print styles in css/style.css (@media print) turn the page into a clean A4 sheet,
// and the user picks "Save as PDF" as the printer.
document.getElementById('descarca-pdf').addEventListener('click', () => window.print());

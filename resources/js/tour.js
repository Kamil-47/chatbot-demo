import Shepherd from "shepherd.js";
import "shepherd.js/dist/css/shepherd.css";

const buttons = {
    back: {
        text: "Wstecz",
        secondary: true,
        action() {
            return this.back();
        },
    },
    next: {
        text: "Dalej",
        action() {
            return this.next();
        },
    },
    finish: {
        text: "Zakończ",
        action() {
            return this.complete();
        },
    },
};

const defaultOptions = {
    useModalOverlay: true,
    defaultStepOptions: {
        cancelIcon: { enabled: true },
        scrollTo: { behavior: "smooth", block: "center" },
        modalOverlayOpeningPadding: 6,
        modalOverlayOpeningRadius: 6,
    },
};

// ========================
// TOUR: STUDENCI
// ========================

const studentTour = new Shepherd.Tour(defaultOptions);

studentTour.addStep({
    id: "student-intro",
    title: "Zarządzanie studentami",
    text: "W tej sekcji masz dostęp do wszystkich uczniów. Dodaj profil ucznia, wpisz obecny teamt, daty sprawdzianów, rób notatki i wiele więcej.",
    buttons: [buttons.next],
});

// ========================
// TOUR: LEKCJE
// ========================

const lessonTour = new Shepherd.Tour(defaultOptions);

lessonTour.addStep({
    id: "lessons-intro",
    title: "Zarządzanie lekcjami",
    text: "Ta sekcja pozwala zarządzać lekcjami wszystkich uczniów. Zobaczysz tu podsumowanie lekcji w wybranym miesiącu.",
    buttons: [buttons.next],
});

lessonTour.addStep({
    id: "lessons-month-picker",
    title: "Wybór miesiąca",
    text: "Wybierz miesiąc z listy, aby filtrować lekcje. Widok zostanie automatycznie odświeżony po zmianie miesiąca.",
    attachTo: {
        element: ".month-picker",
        on: "bottom",
    },
    buttons: [buttons.back, buttons.next],
});

lessonTour.addStep({
    id: "lessons-generate",
    title: "Generowanie lekcji",
    text: 'Kliknij "Generuj lekcje", aby automatycznie utworzyć lekcje dla wszystkich uczniów na wybrany miesiąc. System korzysta z harmonogramu każdego ucznia zdefiniowanego w jego profilu. Jeśli lekcje już istniały - zostaną zastąpione nowymi. Jednocześnie automatycznie zostanie obliczona płatność za ten miesiąc.',
    attachTo: {
        element: ".generate-lessons",
        on: "bottom",
    },
    buttons: [buttons.back, buttons.next],
});

lessonTour.addStep({
    id: "lessons-table",
    title: "Tabela lekcji",
    text: 'Tabela pokazuje podsumowanie dla każdego ucznia: łączną liczbę lekcji w miesiącu oraz podział na odbyte, odwołane i zaplanowane. Kliknij "Zobacz szczegóły", aby zarządzać poszczególnymi lekcjami ucznia.',
    attachTo: {
        element: ".lesson-table",
        on: "top",
    },
    buttons: [buttons.back, buttons.next],
});

lessonTour.addStep({
    id: "lessons-statuses",
    title: "Statusy lekcji",
    text: 'Każda lekcja może mieć jeden z trzech statusów:<br><br><strong>Zaplanowana</strong> - lekcja czeka na realizację<br><strong>Odbyta</strong> - lekcja się odbyła (liczy się do płatności)<br><strong>Odwołana</strong> - lekcja nie doszła do skutku (nie liczy się do płatności)<br><br>Zmiana statusu na "odwołana" automatycznie aktualizuje kwotę płatności za dany miesiąc.',
    buttons: [buttons.back, buttons.finish],
});

// ========================
// TOUR: PŁATNOŚCI
// ========================

const paymentTour = new Shepherd.Tour(defaultOptions);

paymentTour.addStep({
    id: "payments-intro",
    title: "Zarządzanie płatnościami",
    text: "Ta sekcja pokazuje płatności uczniów. Płatności są generowane automatycznie razem z lekcjami i przeliczane na podstawie liczby nieodwołanych lekcji.",
    buttons: [buttons.next],
});

paymentTour.addStep({
    id: "payments-filter",
    title: "Filtrowanie płatności",
    text: "Wybierz miesiąc, aby wyświetlić płatności tylko za ten okres.",
    attachTo: {
        element: ".month-picker",
        on: "bottom",
    },
    buttons: [buttons.back, buttons.next],
});

paymentTour.addStep({
    id: "payments-card",
    title: "Karta płatności",
    text: "Każda karta pokazuje ucznia, liczbę lekcji, kwotę do zapłaty oraz aktualny status. <br> Zielona lewa krawędź, oznacza opłaconą płatność, a czerwona - oczekującą.",
    attachTo: {
        element: ".payment-card",
        on: "bottom",
    },
    showOn() {
        return !!document.querySelector(".payment-card");
    },
    buttons: [buttons.back, buttons.next],
});

paymentTour.addStep({
    id: "payments-mark-paid",
    title: "Oznaczanie płatności jako opłaconej",
    text: 'Kliknij "Oznacz jako opłacone", aby zarejestrować wpłatę ucznia. System automatycznie zapisze dzisiejszą datę jako datę płatności. Możesz też edytować płatność ręcznie, aby ustawić inną datę lub kwotę.',
    buttons: [buttons.back, buttons.finish],
});

// ========================
// START
// ========================

export function startTour() {
    const path = window.location.pathname;

    if (path.includes("/student")) {
        studentTour.start();
    } else if (path.includes("/payment")) {
        paymentTour.start();
    } else if (path.includes("/lesson")) {
        lessonTour.start();
    }
}

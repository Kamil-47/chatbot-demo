// import "./bootstrap";
import { createRoot } from "react-dom/client";
import ChatBot from "./components/ChatBot";
import "shepherd.js/dist/css/shepherd.css";
import Shepherd from "shepherd.js";
import { startTour } from "./tour.js";

document.getElementById("help-tour-btn")?.addEventListener("click", startTour);

// Zamontowanie chatbota
const chatBotContainer = document.getElementById("chatbot-root");
if (chatBotContainer) {
    const root = createRoot(chatBotContainer);
    root.render(<ChatBot />);
}

// TOUR
// const tour = new Shepherd.Tour({
//     useModalOverlay: true,
//     defaultStepOptions: {
//         classes: "shadow-md bg-purple-dark",
//         scrollTo: true,
//     },
// });

// tour.addStep({
//     title: "Strona studentów",
//     text: "Tutaj możesz zarządzać swoimi uczniami. Wpisz profil klasy, wiek, obecny temat lub twórz indywidualne notatki, które pomogą zapamiętać ci słabe punkty twoich uczniów.",
//     attachTo: {
//         element: "students-page",
//     },
//     buttons: [
//         {
//             text: "Next",
//             action: tour.next,
//         },
//     ],
// });

// tour.addStep({
//     text: "Dodawaj nowych uczniów",
//     attachTo: {
//         element: "add-student",
//     },
//     classes: "example-step-extra-class",
//     buttons: [
//         {
//             text: "Next",
//             action: tour.next,
//         },
//     ],
// });

// tour.start();

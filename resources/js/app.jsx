import { createRoot } from "react-dom/client";
import ChatBot from "./components/ChatBot";
import { startTour } from "./tour.js";

const toggle = document.getElementById("sidebar-toggle");
const sidebar = document.getElementById("app-sidebar");
const overlay = document.getElementById("sidebar-overlay");

function setSidebarOpen(open) {
    sidebar?.classList.toggle("is-open", open);
    overlay?.classList.toggle("is-visible", open);
    overlay?.toggleAttribute("hidden", !open);
    toggle?.setAttribute("aria-expanded", String(open));
    document.body.classList.toggle("sidebar-open", open);
}

toggle?.addEventListener("click", () =>
    setSidebarOpen(!sidebar.classList.contains("is-open"))
);
overlay?.addEventListener("click", () => setSidebarOpen(false));
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") setSidebarOpen(false);
});
sidebar
    ?.querySelectorAll("a.nav-item")
    .forEach((a) => a.addEventListener("click", () => setSidebarOpen(false)));

document.getElementById("help-tour-btn")?.addEventListener("click", () => {
    setSidebarOpen(false);
    startTour();
});

const chatBotContainer = document.getElementById("chatbot-root");
if (chatBotContainer) {
    const root = createRoot(chatBotContainer);
    root.render(<ChatBot />);
}

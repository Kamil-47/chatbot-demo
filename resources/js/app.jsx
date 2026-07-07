import { createRoot } from "react-dom/client";
import ChatBot from "./components/ChatBot";
import { startTour } from "./tour.js";

document.getElementById("help-tour-btn")?.addEventListener("click", startTour);

const chatBotContainer = document.getElementById("chatbot-root");
if (chatBotContainer) {
    const root = createRoot(chatBotContainer);
    root.render(<ChatBot />);
}

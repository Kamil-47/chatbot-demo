import React, { useState } from "react";
import ChatBubble from "./ChatBubble";
import ChatWindow from "./ChatWindow";

export default function ChatBot() {
    const [isOpen, setIsOpen] = useState(false);
    const [messages, setMessages] = useState([
        {
            role: "assistant",
            content:
                "Cześć! Jestem asystentem korepetycji. Mogę pomóc w zarządzaniu lekcjami, płatnościami i danymi uczniów. Co chcesz zrobić?",
        },
    ]);
    const [conversationHistory, setConversationHistory] = useState([]);
    const [inputMessage, setInputMessage] = useState("");
    const [isLoading, setIsLoading] = useState(false);

    const toggleChat = () => {
        setIsOpen(!isOpen);
    };

    const sendMessage = async (e) => {
        e.preventDefault();

        if (!inputMessage.trim()) return;

        const userMessage = { role: "user", content: inputMessage };
        setMessages([...messages, userMessage]);
        setInputMessage("");
        setIsLoading(true);

        try {
            const csrfToken =
                document.querySelector('meta[name="csrf-token"]')?.content ?? "";

            const response = await fetch("/api/chat", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    message: inputMessage,
                    conversationHistory: conversationHistory,
                }),
            });

            if (response.status === 429) {
                const data = await response.json().catch(() => ({}));
                setMessages((prev) => [
                    ...prev,
                    {
                        role: "assistant",
                        content:
                            data.response ||
                            "Zbyt wiele wiadomości. Spróbuj ponownie za chwilę.",
                    },
                ]);
                return;
            }

            const data = await response.json();

            // Zaktualizuj historię konwersacji
            if (data.conversationHistory) {
                setConversationHistory(data.conversationHistory);
            }

            // Dodaj odpowiedź asystenta
            const assistantMessage = {
                role: "assistant",
                content: data.response,
            };

            setMessages((prev) => [...prev, assistantMessage]);
        } catch (error) {
            console.error("Error:", error);
            setMessages((prev) => [
                ...prev,
                {
                    role: "assistant",
                    content: "Przepraszam, wystąpił błąd. Spróbuj ponownie.",
                },
            ]);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <>
            <ChatBubble onClick={toggleChat} isOpen={isOpen} />
            {isOpen && (
                <ChatWindow
                    messages={messages}
                    inputMessage={inputMessage}
                    setInputMessage={setInputMessage}
                    sendMessage={sendMessage}
                    isLoading={isLoading}
                    onClose={toggleChat}
                />
            )}
        </>
    );
}

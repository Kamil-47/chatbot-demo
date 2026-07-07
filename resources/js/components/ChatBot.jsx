import React, { useState, useEffect } from "react";
import ChatBubble from "./ChatBubble";
import ChatWindow from "./ChatWindow";

const STORAGE_KEY = "chatbot-state-v1";

const WELCOME_MESSAGE = {
    role: "assistant",
    content:
        "Cześć! Jestem asystentem korepetycji. Mogę pomóc w zarządzaniu lekcjami, płatnościami i danymi uczniów. Co chcesz zrobić?",
};

function loadState() {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed.messages) || !Array.isArray(parsed.conversationHistory)) {
            return null;
        }
        return parsed;
    } catch {
        return null;
    }
}

export default function ChatBot() {
    const initial = loadState();
    const [isOpen, setIsOpen] = useState(initial?.isOpen ?? false);
    const [messages, setMessages] = useState(initial?.messages ?? [WELCOME_MESSAGE]);
    const [conversationHistory, setConversationHistory] = useState(
        initial?.conversationHistory ?? []
    );
    const [inputMessage, setInputMessage] = useState("");
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        try {
            sessionStorage.setItem(
                STORAGE_KEY,
                JSON.stringify({ isOpen, messages, conversationHistory })
            );
        } catch {
            // sessionStorage może być niedostępny (privacy mode) - ignorujemy
        }
    }, [isOpen, messages, conversationHistory]);

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

            if (data.conversationHistory) {
                setConversationHistory(data.conversationHistory);
            }

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

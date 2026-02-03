export async function loadAiStatus() {
    try {
        const response = await fetch("/api/ai/status", {
            headers: {
                Accept: "application/json"
            }
        });

        if (!response.ok) {
            console.warn("Failed to fetch AI status.");
            return { aiAccess: false, aiReady: false };
        }

        const data = await response.json();
        return {
            aiAccess: data.ai_access === true || data.ai_access === 1,
            aiReady: data.ai_ready === true || data.ai_ready === 1
        };
    } catch (error) {
        console.warn("Unable to load AI status.", error);
        return { aiAccess: false, aiReady: false };
    }
}

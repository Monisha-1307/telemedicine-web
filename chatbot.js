function toggleConsultationMode() {
    var formContainer = document.getElementById("form-container");
    var chatbotContainer = document.getElementById("chatbot-container");
    var toggle = document.getElementById("toggle-mode");

    if (toggle.checked) {
        formContainer.style.display = "none";
        chatbotContainer.style.display = "block";
    } else {
        formContainer.style.display = "block";
        chatbotContainer.style.display = "none";
    }
}

function startVoiceRecognition() {
    var recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = "en-US";
    recognition.start();

    recognition.onresult = function(event) {
        var transcript = event.results[0][0].transcript;
        document.getElementById("recognized-text").innerText = "Recognized: " + transcript;
        localStorage.setItem("voice_symptoms", transcript);
    };

    recognition.onerror = function(event) {
        alert("Error in recognition: " + event.error);
    };
}

function submitVoiceData() {
    var symptoms = localStorage.getItem("voice_symptoms");
    if (!symptoms) {
        alert("Please describe your symptoms first.");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "user_dashboard.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert("Consultation request submitted via voice!");
        }
    };
    xhr.send("name=Voice User&age=Unknown&gender=Unknown&symptoms=" + encodeURIComponent(symptoms));
}

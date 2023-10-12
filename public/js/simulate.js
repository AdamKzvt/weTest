toastr.error('Message')

// Affichez le message Toastr sans timeout
toastr.error('Message', {
    "timeOut": 0, // Désactivez le timeout
    "onHidden": function () {
        // Rétablissez le timeout après la disparition de la notification
        toastr.options.timeOut = 8000;
    }
});
document.addEventListener('DOMContentLoaded', function () {


    // Chronomètre
    let chronometerInterval; // harmonisé le nom

    function startChronometer(duration) {
        let startTime = Date.now();
        chronometerInterval = setInterval(() => {
            let elapsedTime = Date.now() - startTime;

            let minutes = Math.floor(elapsedTime / 60000);
            let seconds = Math.floor((elapsedTime % 60000) / 1000);
            let milliseconds = (elapsedTime % 1000).toString().substr(0, 2); // Obtenir les deux premiers chiffres des millisecondes

            let formattedTime = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}.${milliseconds}`;
            document.getElementById('chronometer').textContent = formattedTime; // Utiliser l'ID pour cibler l'élément directement
        }, 10);
    }

    function stopChronometer() {
        clearInterval(chronometerInterval); // Utiliser le bon nom d'intervalle
        document.getElementById('chronometer').innerText = '00:00.00'; // Inclure les millisecondes lors de la réinitialisation
    }

    // Fonction pour simuler un temps d'attente
    function sleep(duration) {
        return new Promise(resolve => setTimeout(resolve, duration));
    }

    function updateSliderLabel(value) {
        const sliderValueElement = document.getElementById('sliderValue');
        sliderValueElement.textContent = value;
    }



    // Fonction pour récupérer les donnees de la dernière simulation
    function fetchLatestSimulation() {
        fetch('/latest-simulation', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.latestSimulation) {
                    // Mise à jour des éléments du DOM avec les nouvelles donnees
                    document.querySelector("#appareils").textContent = data.latestSimulation.Nsimul;
                    document.querySelector("#temperature").textContent = data.latestSimulation.Temperature;
                    document.querySelector("#vitesse").textContent = data.latestSimulation.Velocity;
                    document.querySelector("#flux").textContent = data.latestSimulation.Flow;
                    document.querySelector("#energie").textContent = data.latestSimulation.Energy;
                    document.querySelector("#panne").textContent = data.latestSimulation.Failure;
                    document.querySelector("#active").textContent = new Date(data.latestSimulation.Start).toLocaleString('fr-FR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    const duration = data.latestSimulation.Duration;
                    if (duration && typeof duration === "string") {
                        document.querySelector("#duree").textContent = "Durée: " + duration;
                    } else {
                        console.error("Invalid duration:", data.latestSimulation.Duration);
                    }

                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des donnees:', error);
            });
    }



    // Fonction pour afficher les spinners
    function showSpinners() {
        const spinners = document.querySelectorAll('.spinner-border');
        spinners.forEach(spinner => {
            spinner.style.display = 'inline-block';
        });
    }

    // Fonction pour cacher les spinners
    function hideSpinners() {
        const spinners = document.querySelectorAll('.spinner-border');
        spinners.forEach(spinner => {
            spinner.style.display = 'none';
        });
    }

    function hideData() {
        const dataElements = document.querySelectorAll('span[id]');
        dataElements.forEach(element => {
            if (element.id !== 'chronometer') { // ne pas cacher le chronomètre
                element.style.display = 'none';
            }
        });
    }

    function showData() {
        const dataElements = document.querySelectorAll('span[id]');
        dataElements.forEach(element => {
            element.style.display = 'inline-block';
        });
    }

    const runSimulation = async function () {
       

        // Enregistrer le moment de début
        const startTime = Date.now();
        hideData(); // Cacher les donnees
        showSpinners(); // Afficher le spinner lors du démarrage de la simulation


        startChronometer(); // Démarrer le chronomètre

        let formData = new FormData(document.getElementById('simulation-form'));
        const simulationDuration = formData.get('module_choice[simulation_duration]') * 1000;
        const failureProbability = 0.25;
        const randomValue = Math.random();
        let failureOccurred = 0; // Ajout d'une variable pour stocker si une panne s'est produite ou non

        if (randomValue < failureProbability) {
            const failureStart = Math.floor(Math.random() * simulationDuration);
            const failureDuration = Math.floor(Math.random() * (simulationDuration - failureStart));

            await sleep(failureStart);
            toastr.error('Le module est en panne!');
            failureOccurred = 1; // Mise à jour de la variable lors d'une panne

            await sleep(failureDuration);
            toastr.info('Le module refonctionne.');

            await sleep(simulationDuration - failureStart - failureDuration);
        } else {
            await sleep(simulationDuration);
        }

        // Enregistrer le moment de fin
        const endTime = Date.now();

        // Calculer la durée réelle
        const realSimulationDuration = endTime - startTime;
        formData.append('real_simulation_duration', realSimulationDuration);


        // Ajouter la valeur de panne à FormData
        formData.append('failureOccurred', failureOccurred);

        fetch('/simulate', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                hideSpinners(); // Cacher le spinner après la simulation
                stopChronometer();
                if (!response.ok) {
                    throw new Error('Erreur réseau ou côté serveur');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.status === 'success') {
                    showData(); // Montrer les donnees
                    console.log("Simulation terminée.");
                    fetchLatestSimulation(); // Ajout de l'appel à la nouvelle fonction ici
                    submitButton.disabled = false;
                } else if (data && data.status === 'failure') {
                    console.error(data.message);
                    alert(data.message);
                    submitButton.disabled = false;
                } else {
                    console.error('Réponse inattendue du serveur');
                }
            })
            .catch(error => {
                hideSpinners(); // Cacher le spinner en cas d'erreur pendant la simulation
                console.error('Erreur:', error);
                alert('Une erreur est survenue pendant la simulation.');
                submitButton.disabled = false;
            });
    };


    let currentModuleId = null;
    const submitButton = document.querySelector('#simulation-form button[type="submit"]');

    document.getElementById('simulation-form').addEventListener('submit', function (event) {
        event.preventDefault();
        submitButton.disabled = true;
        console.log("Simulation en cours...");

        const moduleChoiceField = this.querySelector('[name="module_choice[module]"]');
        currentModuleId = moduleChoiceField ? moduleChoiceField.value : null;
        runSimulation();
    });

    document.getElementById('stop-simulation-button').addEventListener('click', function () {
        hideSpinners(); // Cacher le spinner lors de l'interruption de la simulation
        stopChronometer(); // Arrêter le chronomètre lors de l'interruption
        if (!currentModuleId) {
            console.error('Aucun module en cours de simulation');
            return;
        }
        submitButton.disabled = false;
        fetch(`/stop-simulation/${currentModuleId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    console.error('Erreur lors de l’arrêt de la simulation côté serveur');
                }
            });
    });

    // Event listener pour mettre à jour la valeur affichée du slider
    const simulationDurationSlider = document.querySelector('[name="module_choice[simulation_duration]"]');
    if (simulationDurationSlider) {
        simulationDurationSlider.addEventListener('input', function () {
            updateSliderLabel(this.value);
        });
    }
});







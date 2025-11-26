// Select the div by class
const redirectPetOwners = document.querySelector('.petowners-js');
const redirectDashboard = document.querySelector('.dashboard-js');
const redirectAppointment = document.querySelector('.appointment-js');
const redirectSchedule = document.querySelector('.schedule-js');
const redirectGroomer = document.querySelector('.groomer-js');


// Add a click event listener to the div
redirectPetOwners?.addEventListener('click', () => {
    window.location.href = 'petOwner.php';
});

redirectDashboard?.addEventListener('click', () => {
    window.location.href = 'dashboard.php';
});

redirectAppointment?.addEventListener('click', () => {
    window.location.href = 'appointment.php';
});

redirectSchedule?.addEventListener('click', () => {
    window.location.href = 'schedule.php';
});

redirectGroomer?.addEventListener('click', () => {
    window.location.href = 'groomer.php';
});



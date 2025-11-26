const redirectHome = document.querySelector('.home-js');
const redirectGroomers = document.querySelector('.groomers-js');
const redirectBookschedule = document.querySelector('.bookSched-js');
const redirectBookings = document.querySelector('.bookings-js'); 
const redirectSettings = document.querySelector('.settings-js');

// Users
redirectHome?.addEventListener('click', () => {
  window.location.href = 'user-home.php';
});

redirectGroomers?.addEventListener('click', () => {
  window.location.href = 'user-groomers.php';
});

redirectBookschedule?.addEventListener('click', () => {
  window.location.href = 'user-schedule.php';
});

redirectBookings?.addEventListener('click', () => {
  window.location.href = 'user-bookings.php';
});

redirectSettings?.addEventListener('click', () => {
  window.location.href = 'user-settings.php';
});



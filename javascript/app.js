// Global variables
const roomIDInput = document.querySelector('#room-id-input');
const buildingHeader = document.querySelector('#building-header');
const roomNumberHeader = document.querySelector('#room-number-header');
const bookingsContainer = document.querySelector('#bookings-container');
const bookingForm = document.querySelector('#booking-form');
const datePicker = document.querySelector('#date-picker');

// For sidebar

function updateBookingsForRoomClick() {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        bookingsContainer.innerHTML = this.responseText;
      }
    };
    xmlhttp.open("GET", "get-bookings.php?room_id=" + roomIDInput.value, true);
    xmlhttp.send();
}

function updateRoomArea(event) {
    bookingForm.reset();
    roomIDInput.value = event.target.dataset.roomid;
    buildingHeader.innerHTML = event.target.dataset.building;
    roomNumberHeader.innerHTML = event.target.innerHTML;
    updateBookingsForRoomClick();
}

// For timepicker and Booking

function addEventListenersToRemoveBookings() {
  const removeBookings = document.querySelectorAll('.remove-bookings');
  Array.from(removeBookings).forEach(function(element) {
      element.addEventListener('click', removeBooking);
    });
}

function addEventListenersToBookButtons() {
  const bookButtons = document.querySelectorAll('.book-buttons');
  Array.from(bookButtons).forEach(function(element) {
      element.addEventListener('click', addBooking);
    });
}

function updateTimeSelect() {
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      bookingsContainer.innerHTML = this.responseText;
      addEventListenersToBookButtons();
      addEventListenersToRemoveBookings();
    }
  };
  xmlhttp.open("GET", "time-select.php?date_picked=" + datePicker.value + "&room_id=" + roomIDInput.value, true);
  xmlhttp.send();
}

function removeBooking(event) {
  const bookingId = event.target.dataset.bookingid;
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      bookingsContainer.innerHTML = this.responseText;
      addEventListenersToBookButtons();
      addEventListenersToRemoveBookings();
    }
  };
  xmlhttp.open("GET", "delete-booking.php?date_picked=" + datePicker.value + "&room_id=" + roomIDInput.value + "&booking_id=" + bookingId);
  xmlhttp.send();
}

function addBooking(event) {
  const pickedHour = event.target.dataset.hour;
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      bookingsContainer.innerHTML = this.responseText;
      addEventListenersToBookButtons();
      addEventListenersToRemoveBookings();
    }
  };
  xmlhttp.open("POST", "add-booking.php");
  xmlhttp.setRequestHeader("Content-Type", "application/json");
  xmlhttp.send(JSON.stringify({"hour": pickedHour, "date_picked": datePicker.value, "room_id": roomIDInput.value}));
}




// For booking




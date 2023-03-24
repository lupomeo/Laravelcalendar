<x-app-layout>
    <x-slot name="header">
        
    </x-slot>
  
<div class=container border" style="padding-left:30px;padding-right:30px; padding-top:15px; padding-bottom:15px; background:white;">
    
    <div id='calendar'></div>
</div>
              <div class="modal fade show" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white" id="exampleModalLongTitle">Appuntamento</h5>
                            <button id="modalClose1" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="javascript:void(0)" id="ProductForm" name="ProductForm" method="POST" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="id" id="id">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Titolo:</label>
                                    <input type="text" name="title" class="form-control" placeholder="Titolo" maxlength="200" required id="id_title">
                                </div>
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Descrizione:</label>
                                    <textarea name="description" cols="40" rows="10" class="form-control" placeholder="Descrizione" required id="id_description">
                                    </textarea>
                                </div>
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Data inizio:</label>
                                    <input type="datetime-local" name="start_time" class="form-control" required id="id_start_time">
                                </div>
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Data fine:</label>
                                    <input type="datetime-local" name="end_time" class="form-control" required id="id_end_time">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="modalClose2" type="button" class="btn btn-secondary" >Annulla</button>
                                <button id="modalClose3" type="button" class="btn btn-danger" onclick="deleteFunc()">Elimina</button>
                                <button type="submit" class="btn btn-primary">Salva</button>
                            </div>
                        </form>
                    </div>
                </div>
              </div>
   


  

  <script src="js/it.js"></script>
  <script>
      var calendar;
      document.addEventListener('DOMContentLoaded', function() {

        var SITEURL = "{{ url('/') }}";
        var today = new Date();
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
        
          locale: 'it',
          slotMinTime: '08:00',
          slotMaxTime: '20:00',
          slotDuration: '00:30:00',
          expandRows: true,
          eventShortHeight: '50',
          weekends: false,
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,list'
          },
          initialDate: today,
          navLinks: true, // can click day/week names to navigate views
          selectable: true,
          selectMirror: true,
          editable: true,
          dayMaxEvents: true, // allow "more" link when too many events
          // THIS KEY WON'T WORK IN PRODUCTION!!!
          // To make your own Google API key, follow the directions here:
          // http://fullcalendar.io/docs/google_calendar/
          googleCalendarApiKey: 'AIzaSyBkWiMG51Qq2AMMgiP-l3QMma7CusGq2Ks',
          eventSources: [ SITEURL + "/getevents", 'it.italian#holiday@group.v.calendar.google.com' ],

          select: function(arg) {
            var view = calendar.view;
            if(view.type == "timeGridDay") {
              var modal = document.getElementById('eventModal');
              modal.style.display = 'block';
              $('#ProductForm').trigger("reset");
              document.getElementById('modalClose3').disabled = true;
              const id = document.querySelector('#id');
              id.value = ('');
              const dateControl = document.querySelector('#id_start_time');
              dateControl.value = arg.startStr.substring(0, arg.startStr.length - 6);
              const dateControl2 = document.querySelector('#id_end_time');
              dateControl2.value = arg.endStr.substring(0, arg.endStr.length - 6);
            } else  {
              calendar.changeView('timeGridDay', arg.start);
            }   
            calendar.unselect()
          },
          

          eventClick: function(arg) {
            arg.jsEvent.preventDefault();
            editFunc(arg.event.id);
          },
          
          

        });

        calendar.render();
      });
      const closeBtn1 = document.getElementById('modalClose1');
      const closeBtn2 = document.getElementById('modalClose2');
      closeBtn1.addEventListener('click',()=>{
        const eventModal = document.getElementById('eventModal')
        eventModal.style.display = 'none';
      });
      closeBtn2.addEventListener('click',()=>{
        const eventModal = document.getElementById('eventModal')
        eventModal.style.display = 'none';
      });


      $('#ProductForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('fullcalenderAjax') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                  const eventModal = document.getElementById('eventModal')
                  eventModal.style.display = 'none';
                  displayMessage("Dati inseriti");
                  
                  calendar.refetchEvents();
                },
                error: function(data) {
                    console.log(data);
                    alert("Errore nell'inserimento dati.");
                }
            });
      }); 
    function displayMessage(message) {
        toastr.success(message, 'Evento');
      }
    
    function editFunc(id) {
            $.ajax({
                type: "POST",
                url: "{{ url('editevent') }}",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                  var modal = document.getElementById('eventModal');
                  modal.style.display = 'block';
                  document.getElementById('modalClose3').disabled = false;
                  $('#id').val(res.id);
                  $('#id_title').val(res.title);
                  $('#id_description').val(res.description);
                  $('#id_start_time').val(res.start);
                  $('#id_end_time').val(res.end);
                }
            });
    }
    function deleteFunc() {
      var id = document.getElementById('id').value;
      if (id) {    
        if (confirm('Sei sicuro di cancellare questo evento?')) {
          $.ajax({
                    type: "POST",
                    url: "{{ url('deleteevent') }}",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                      const eventModal = document.getElementById('eventModal')
                      eventModal.style.display = 'none';
                      displayMessage("Evento cancellato");
                      calendar.refetchEvents();
                    }
                });
        }
      } else {
        alert("Evento non cancellabile"); 
      }
    
    }
  </script>
  </x-app-layout>
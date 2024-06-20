$(document).ready(function() { // ta funkcija caka de je stran do konca zlodana

    function loadTasks() { // funkcija za load-anje ali reload-anje vseh task-ov
        $.ajax({ // ajax request
            url: '/get_all_tasks', // zahteva
            type: 'GET', // metoda zahteve
            dataType: 'json', // kateri tip pricakujemo nazaj
            success: function(tasks) { // ce je uspesno se izvede ta funkcija
                $('#task-list').empty(); // Najprej pocistimo task-list div da lahko vanj vsnesemo nove

                function priorityToWordAndColor(priority) { // pridobi besedo in barvo glede na to katera stevilka prioritete je
                    switch (priority) {
                        case 1:
                            return { word: 'Low', colorClass: 'priority-low' };
                        case 2:
                            return { word: 'Medium', colorClass: 'priority-medium' };
                        case 3:
                            return { word: 'Normal', colorClass: 'priority-normal' };
                        case 4:
                            return { word: 'High', colorClass: 'priority-high' };
                        case 5:
                            return { word: 'Critical', colorClass: 'priority-critical' };
                        default:
                            return { word: '', colorClass: '' }; // Ce stevilke ni v seznamu ne vrne nic
                    }
                }

                // gremo skozi vse taske katere smo dobili od serverja in vsakema ustvarimo na novo v div task-list
                tasks.forEach(function(task) {
                    var priorityInfo = priorityToWordAndColor(task.priority); // dobimo besedo in barvo prioritete
                    var taskHtml = '<div class="task" id="task-' + task.id + '">' +
                                   '<div>' +
                                   '<p>' + task.task + '</p>' +
                                   '<p>Completed: <span class="' + (task.is_completed ? 'completed' : 'not-completed') + '">' + (task.is_completed ? 'Yes' : 'No') + '</span></p>' +
                                   '<p>Priority: <span class="task ' + priorityInfo.colorClass + '">' + priorityInfo.word + '</span></p>' +
                                   '</div>' +
                                   '<div class="task-buttons">';
                
                    if (!task.is_completed) { // ce task ni se upravljen mu damo gumb za uznacevanje da je upravljen
                        taskHtml += '<button class="btn btn-mark-complete" data-id="' + task.id + '">Mark As Complete</button>';
                    } // v nasprotnem primeru mu ga ne damo saj ni potreben
                    
                    taskHtml += '<button class="btn btn-edit" data-id="' + task.id + '">Edit</button>' +
                                '<button class="btn btn-delete" data-id="' + task.id + '">Delete</button>' +
                                '</div>' +
                                '</div>';
                    $('#task-list').append(taskHtml); // na koncu se task-list div-u dodamo (append) nov task div
                });

                // Vsem gumbom v div-ih dodamo event listener-je za vse gumbe
                $('.btn-edit').on('click', function() {
                    var taskId = $(this).data('id');
                    editTask(taskId);
                });

                $('.btn-delete').on('click', function() {
                    var taskId = $(this).data('id');
                    deleteTask(taskId);
                });

                $('.btn-mark-complete').on('click', function() {
                    var taskId = $(this).data('id');
                    markTaskComplete(taskId);
                });
            },
            error: function(xhr, status, error) {
                alert('An error occurred while loading tasks: ' + error);
            }
        });
    }

    // Ko se stran prvic zloada prikazemo vse taske
    loadTasks();

    // Event handler za ko zelimo odpreti modal/popup za dodajanje novega task-a
    $('#openModal').on('click', function() {
        $('#taskModal').css('display', 'block');
    });

    // Ko zelimo zapreti modal/popup s klikom na x
    $('.btn-close').on('click', function() {
        $('#taskModal').css('display', 'none');
    });

    // Ko zelimo zapreti modal/popup s klikom izven njega
    $(window).on('click', function(event) {
        if (event.target == $('#taskModal')[0]) {
            $('#taskModal').css('display', 'none');
        }
    });

    // Form submission
    $('#modalTaskForm').on('submit', function(e) { // ko v modal-u/popup-u kliknemo na submit se izvede ta funkcija
        e.preventDefault(); // preventamo default behavior

        var taskDesc = $('#modal_task_desc').val(); // pridobimo opis task-a
        var taskPriority = $('#modal_task_priority').val();

        $.ajax({ // poslemo zahtevo strezniku za dodajanje task-a
            url: '/add_task',
            type: 'POST',
            data: { task_desc: taskDesc, task_priority: taskPriority }, // dodamo opis task-a
            dataType: 'json', // v obliki json
            success: function(response) { // on success
                if (response.status === 'success') {
                    loadTasks(); // Reload tasks
                    $('#taskModal').css('display', 'none'); // zapremo popup tako da css property display nastavimo na none
                } else {
                    alert(response.message); // ce pride do napake jo javimo
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error); // ce pride do napake jo javimo
            }
        });
    });

    // Function to mark task as complete
    function markTaskComplete(taskId) {
        $.ajax({
            url: '/mark_task_complete',
            type: 'POST',
            data: { task_id: taskId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    loadTasks(); // Reload tasks
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while marking task as complete: ' + error);
            }
        });
    }

    // Delete task function with confirmation
    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            $.ajax({
                url: '/delete_task',
                type: 'POST',
                data: { task_id: taskId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#task-' + taskId).remove();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while deleting task: ' + error);
                }
            });
        }
    }

    // Edit task function
    function editTask(taskId) {
        var newTaskDesc = prompt('Enter new task description:');
        if (newTaskDesc) {
            $.ajax({
                url: '/edit_task',
                type: 'POST',
                data: { task_id: taskId, task_desc: newTaskDesc },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        loadTasks(); // Reload tasks
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while editing task: ' + error);
                }
            });
        }
    }
});

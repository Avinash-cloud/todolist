<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body class="bg-gray-50 min-h-screen py-8">
    <div class=" mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">To-Do List</h1>

        </div>

        <div class="flex mb-6 gap-2">
            <input type="text" id="task-input" placeholder="Enter a task"
                class="flex-grow px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button id="add-task"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add
            </button>
        </div>

        <div class="flex justify-between mb-4">
            <div class="flex gap-2">
                <button id="show-all" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-lg text-sm transition">
                    Show All
                </button>
                <button id="clear-completed" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-lg text-sm transition">
                    Clear Completed
                </button>
            </div>
            <div id="task-count" class="text-sm text-gray-500">
                {{ count($tasks) }} tasks
            </div>
        </div>

        <ul id="task-list" class="divide-y divide-gray-200">
            @foreach ($tasks as $task)
            <li data-id="{{ $task->id }}" class="task-item py-3 px-2 hover:bg-gray-50 rounded-lg flex items-center justify-between group {{ $task->completed ? 'completed opacity-75' : '' }}">
                <div class="flex items-center">
                    <button class="complete-task mr-3 p-1 rounded-full hover:bg-gray-200 transition">
                        @if($task->completed)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @endif
                    </button>
                    <span class="{{ $task->completed ? 'line-through text-gray-400' : 'text-gray-700' }}">{{ $task->task }}</span>
                </div>
                <button class="delete-task opacity-0 group-hover:opacity-100 p-1 rounded-full hover:bg-red-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </li>
            @endforeach
        </ul>

        @if(count($tasks) === 0)
        <div class="text-center py-8 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p>No tasks yet. Add one above!</p>
        </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            // Initialize sortable
            new Sortable(document.getElementById('task-list'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    // You could implement reordering logic here if needed
                }
            });

            // Add Task
            $('#add-task').click(addTask);
            $('#task-input').keypress(function(e) {
                if (e.which === 13) addTask();
            });

            function addTask() {
                let task = $('#task-input').val().trim();
                if (task === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Task cannot be empty!',
                        confirmButtonColor: '#3B82F6',
                    });
                    return;
                }

                $.post('/tasks', {
                    task: task,
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                    $('#task-list').append(`
                        <li data-id="${data.id}" class="task-item py-3 px-2 hover:bg-gray-50 rounded-lg flex items-center justify-between group">
                            <div class="flex items-center">
                                <button class="complete-task mr-3 p-1 rounded-full hover:bg-gray-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <span class="text-gray-700">${data.task}</span>
                            </div>
                            <button class="delete-task opacity-0 group-hover:opacity-100 p-1 rounded-full hover:bg-red-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </li>
                    `);
                    $('#task-input').val('');
                    updateTaskCount();
                    if ($('#task-list li').length === 1) {
                        $('#task-list').find('.no-tasks-message').remove();
                    }
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.errors.task[0],
                        confirmButtonColor: '#3B82F6',
                    });
                });
            }

            // Delete Task
            $(document).on('click', '.delete-task', function(e) {
                e.stopPropagation();
                let listItem = $(this).closest('li');
                let id = listItem.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3B82F6',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/tasks/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                listItem.fadeOut(300, function() {
                                    $(this).remove();
                                    updateTaskCount();
                                    if ($('#task-list li').length === 0) {
                                        $('#task-list').append(`
                                            <div class="no-tasks-message text-center py-8 text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <p>No tasks yet. Add one above!</p>
                                            </div>
                                        `);
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Complete Task
            $(document).on('click', '.complete-task', function(e) {
                e.stopPropagation();
                let listItem = $(this).closest('li');
                let id = listItem.data('id');
                let isCompleted = listItem.hasClass('completed');

                $.post(`/tasks/complete/${id}`, {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        listItem.toggleClass('completed opacity-75');
                        let taskText = listItem.find('span');
                        taskText.toggleClass('line-through text-gray-400 text-gray-700');

                        // Toggle the checkmark icon
                        let icon = listItem.find('.complete-task svg');
                        if (listItem.hasClass('completed')) {
                            icon.replaceWith(`
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            `);
                        } else {
                            icon.replaceWith(`
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            `);
                        }
                    }
                });
            });

            // Show All Tasks
            $('#show-all').click(function() {
                $.get('/tasks/show', function(data) {
                    $('#task-list').empty();
                    if (data.length === 0) {
                        $('#task-list').append(`
                            <div class="no-tasks-message text-center py-8 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p>No tasks yet. Add one above!</p>
                            </div>
                        `);
                    } else {
                        data.forEach(task => {
                            $('#task-list').append(`
                                <li data-id="${task.id}" class="task-item py-3 px-2 hover:bg-gray-50 rounded-lg flex items-center justify-between group ${task.completed ? 'completed opacity-75' : ''}">
                                    <div class="flex items-center">
                                        <button class="complete-task mr-3 p-1 rounded-full hover:bg-gray-200 transition">
                                            ${task.completed ? 
                                                `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>` :
                                                `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>`
                                            }
                                        </button>
                                        <span class="${task.completed ? 'line-through text-gray-400' : 'text-gray-700'}">${task.task}</span>
                                    </div>
                                    <button class="delete-task opacity-0 group-hover:opacity-100 p-1 rounded-full hover:bg-red-100 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </li>
                            `);
                        });
                    }
                    updateTaskCount();
                });
            });

            // Clear Completed Tasks
            // Clear Completed Tasks
            $('#clear-completed').click(function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will remove all completed tasks!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3B82F6',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Yes, clear them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/tasks/clear-completed',
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}', // Laravel will interpret this as a DELETE request
                            },
                            success: function(response) {
                                // Remove all completed tasks from UI
                                $('#task-list li.completed').fadeOut(300, function() {
                                    $(this).remove();
                                    updateTaskCount();

                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        confirmButtonColor: '#3B82F6',
                                    });

                                    // Show empty state if no tasks left
                                    if ($('#task-list li').length === 0) {
                                        $('#task-list').append(`
                                <div class="no-tasks-message text-center py-8 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p>No tasks yet. Add one above!</p>
                                </div>
                            `);
                                    }
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to clear completed tasks',
                                    confirmButtonColor: '#3B82F6',
                                });
                            }
                        });
                    }
                });
            }); // Update task count



            function updateTaskCount() {
                let count = $('#task-list li').length;
                $('#task-count').text(count + ' task' + (count !== 1 ? 's' : ''));
            }
        });
    </script>
</body>

</html>
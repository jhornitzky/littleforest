<html>
<head>
    <title>littleforest</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/app.css">
</head>
<body>
    <section class="todoapp">
      <header class="header">
        <h1>todos</h1>
        <input class="new-todo"
          autofocus autocomplete="off"
          placeholder="What needs to be done?"
          v-model="newTodo"
          @keyup.enter="addTodo">
      </header>
      <section class="main" v-show="todos.length" v-cloak>
        <input class="toggle-all" type="checkbox" v-model="allDone">
        <ul class="todo-list">
          <li v-for="todo in filteredTodos"
            class="todo"
            :key="todo.id"
            :class="{ completed: todo.completed, editing: todo == editedTodo }">
            <div class="view">
              <input class="toggle" type="checkbox" v-model="todo.completed">
              <label @dblclick="editTodo(todo)">{{ todo.title }}</label>
              <button class="destroy" @click="removeTodo(todo)"></button>
            </div>
            <input class="edit" type="text"
              v-model="todo.title"
              v-todo-focus="todo == editedTodo"
              @blur="doneEdit(todo)"
              @keyup.enter="doneEdit(todo)"
              @keyup.esc="cancelEdit(todo)">
          </li>
        </ul>
      </section>
      <footer class="footer" v-show="todos.length" v-cloak>
        <span class="todo-count">
          <strong>{{ remaining }}</strong> {{ remaining | pluralize }} left
        </span>
        <ul class="filters">
          <li><a href="#/all" :class="{ selected: visibility == 'all' }">All</a></li>
          <li><a href="#/active" :class="{ selected: visibility == 'active' }">Active</a></li>
          <li><a href="#/completed" :class="{ selected: visibility == 'completed' }">Completed</a></li>
        </ul>
        <button class="clear-completed" @click="removeCompleted" v-show="todos.length > remaining">
          Clear completed
        </button>
      </footer>
    </section>
    <footer class="info">
      <p>Double-click to edit a todo</p>
      <p>Written by <a href="http://evanyou.me">Evan You</a></p>
      <p>Part of <a href="http://todomvc.com">TodoMVC</a></p>
    </footer>

    <!-- scripts -->
    <script src="js/bootstrap.js"></script>
    <script src="js/vue.js"></script>
    <script src="js/vue-localstorage.js"></script>
    <script type="text/javascript">
        // localStorage persistence
        var STORAGE_KEY = 'littleforest'
        var todoStorage = {
          fetch: function () {
            var todos = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
            todos.forEach(function (todo, index) {
              todo.id = index
            })
            todoStorage.uid = todos.length
            return todos
          },
          save: function (todos) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(todos))
          }
        }

        //app
        var app = new Vue({
          // app initial state
          data: {
            todos: todoStorage.fetch(),
            newTodo: '',
            editedTodo: null,
            visibility: 'all'
          },

          // watch todos change for localStorage persistence
          watch: {
            todos: {
              handler: function (todos) {
                todoStorage.save(todos)
              },
              deep: true
            }
          },

          // note there's no DOM manipulation here at all.
          methods: {
            addTodo: function () {
              var value = this.newTodo && this.newTodo.trim()
              if (!value) {
                return
              }
              this.todos.push({
                id: todoStorage.uid++,
                title: value,
                completed: false
              })
              this.newTodo = ''
            },

            removeTodo: function (todo) {
              this.todos.splice(this.todos.indexOf(todo), 1)
            },

            editTodo: function (todo) {
              this.beforeEditCache = todo.title
              this.editedTodo = todo
            },

            doneEdit: function (todo) {
              if (!this.editedTodo) {
                return
              }
              this.editedTodo = null
              todo.title = todo.title.trim()
              if (!todo.title) {
                this.removeTodo(todo)
              }
            },

            cancelEdit: function (todo) {
              this.editedTodo = null
              todo.title = this.beforeEditCache
            },

            removeCompleted: function () {
              this.todos = filters.active(this.todos)
            }
          },
        });

        app.$mount('.todoapp')
    </script>
</body>
</html>

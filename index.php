<html>
<head>
    <title>littleforest</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/app.css">
</head>
<body>
    <section class="todoapp">
      <header class="header">
        <h1>littleforest</h1>
        <input class="new-todo"
          autofocus autocomplete="off"
          placeholder="today is a lovely day"
          v-model="newTodo"
          @keyup.enter="addTodo">
      </header>
      <section class="main" v-show="todos.length" v-cloak>
        <ul class="todo-list">
          <li v-for="todo in filteredTodos"
            class="todo"
            :key="todo.id">
            <input class="edit" type="text"
              v-model="todo.title"
              v-todo-focus="todo == editedTodo"
              @blur="doneEdit(todo)"
              @keydown.delete="checkForDelete(todo)"
              @keyup.enter="doneEdit(todo)"
              @keyup.esc="cancelEdit(todo)">
          </li>
        </ul>
      </section>
    </section>
    <footer class="info">
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

          // computed properties
          // http://vuejs.org/guide/computed.html
          computed: {
            filteredTodos: function () {
              return this.todos
            },
          },

          // note there's no DOM manipulation here at all.
          methods: {
            addTodo: function () {
              var value = this.newTodo && this.newTodo.trim()
              if (!value) {
                return
              }
              this.todos.splice(0, 0,{
                id: todoStorage.uid++,
                title: value,
                completed: false
              })
              this.newTodo = ''
              Vue.nextTick(function() {
                this.todos[10].$els.inputElement.focus();
              });
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
                var i = this.todos.indexOf(todo);
                this.removeTodo(todo)
              }
            },

            checkForDelete: function (todo) {
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

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
          @keyup.enter.prevent="addTodo"
          @keyup.down.prevent="toFirst">
      </header
      <section class="main" v-show="todos.length" v-cloak>
        <ul class="todo-list">
          <li v-for="(todo,key) in filteredTodos"
            class="todo"
            :key="todo.id">
            <input class="edit" type="text"
              v-model="todo.title"
              v-todo-focus="key === focusKey"
              @blur="doneEdit(todo)"
              @keydown.delete="checkForDelete(todo)"
              @keyup.enter.prevent="lineEnter(todo)"
              @keyup.esc="cancelEdit(todo)"
              @keyup.up.prevent="moveUp(todo)"
              @keyup.down.prevent="moveDown(todo)">
          </li>
        </ul>
      </section>
    </section>
    <footer class="info">
    </footer>

    <!-- scripts -->
    <!--<script src="js/bootstrap.js"></script> -->
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
            visibility: 'all',
            focusKey:-1
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
              this.newTodo = ''
              this.todos.splice(0, 0,{
                id: todoStorage.uid++,
                title: value,
                completed: false
              })
            },

            removeTodo: function (todo) {
              this.todos.splice(this.todos.indexOf(todo), 1)
            },

            editTodo: function (todo) {
              this.beforeEditCache = todo.title
              this.editedTodo = todo
            },

            doneEdit: function (todo) {
              todo.title = todo.title.trim()
            },

            lineEnter: function (todo) {
              todo.title = todo.title.trim()
              this.todos.splice(this.todos.indexOf(todo)+1, 0, {
                id: todoStorage.uid++,
                title: '',
                completed: false
              })
            },

            checkForDelete: function (todo) {
              todo.title = todo.title.trim()
              if (!todo.title) {
                var i = this.todos.indexOf(todo)
                if (i > 0) this.focusKey = i-1; //stay in place
                this.removeTodo(todo)
                Vue.nextTick(function(){
                    this.focusKey = -1;
                });
              }
            },

            moveUp: function(todo) {
                var i = this.todos.indexOf(todo)
                this.focusKey = i-1;
                if (this.focusKey < 0)

                Vue.nextTick(function(){
                    this.focusKey = -1
                });
            },

            moveDown: function(todo) {
                var i = this.todos.indexOf(todo)
                if (i != this.todos.length-1) this.focusKey = i+1
                Vue.nextTick(function(){
                    this.focusKey = -1
                });
            },

            toFirst: function() {
                this.focusKey = 0
                Vue.nextTick(function(){
                    this.focusKey = -1
                });
            }

          },

          // a custom directive to wait for the DOM to be updated
          // before focusing on the input field.
          // http://vuejs.org/guide/custom-directive.html
          directives: {
            'todo-focus': function (el, binding) {
              if (binding.value) el.focus()
            }
          }
        });

        app.$mount('.todoapp')
    </script>
</body>
</html>

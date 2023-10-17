<template>
    <div class="container">
        <div class="interface">
            <button v-if="!importUsers" v-on:click="startImport">Импортировать пользователей</button>
            <div v-else>Импортировать пользователей</div>
            <div class="import-status">
                <span class="import-all">Всего: {{allUsersCount}}</span>
                <span class="import-added">Добавлено: {{addedUsersCount}}</span>
                <span class="import-updated">Обновлено: {{updatedUsersCount}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    export default {
        data() {
            return {
                countImportedUsers: 0,
                fetchUsers: false,
                importUsers: false,
                finishImport: false,
                statusImport: 0,
                allUsersCount: 0,
                addedUsersCount: 0,
                updatedUsersCount: 0,
            }
        },
        mounted() {
            this.getAllUsers();
            this.openWebSocketConnection();
        },
        methods: {
            startImport: function() {
                this.importUsers = true;
                axios.get('/api/import').then((response) => {
                    this.importUsers = response.data;
                    console.log(response.data);
                })
            },
            getAllUsers() {
                axios.get('/api/users').then((response) => {
                    this.allUsersCount = response.data['result']['users_count'];
                })
            },
            openWebSocketConnection() {
                const socket = new WebSocket(import.meta.env.VITE_WEBSOCKET_URI);
                socket.onopen = function() {
                    console.log("Соединение установлено.");
                };

                socket.onclose = function(event) {
                    if (event.wasClean) {
                        alert('Соединение закрыто чисто');
                    } else {
                        alert('Обрыв соединения');
                    }
                    alert('Код: ' + event.code + ' причина: ' + event.reason);
                };

                socket.onmessage = function(event) {
                    let data = JSON.parse(event.data);
                    switch (data['type']){
                       case 'import_user_count':
                           this.countImportedUsers = data['data']['import_user_count'];
                            break;
                       case 'insert_user_count':
                           this.addedUsersCount += data['data']['insert_user_count'];
                           this.countImportedUsers -= data['data']['insert_user_count'];
                           break;
                        case 'update_user_count':
                            this.updatedUsersCount += data['data']['update_user_count'];
                            this.countImportedUsers -= data['data']['update_user_count'];
                            break;
                   }
                   if (this.countImportedUsers === 0) {
                       this.importUsers = false;
                   }
                }.bind(this);

                socket.onerror = function(error) {
                    alert("Ошибка " + error.message);
                };
            }
        }
    }
</script>
<style>
    span {
        margin-right: 10px;
    }
</style>

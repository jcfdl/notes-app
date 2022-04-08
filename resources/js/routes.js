import Notes from './components/Notes';
import Login from './components/Login';
import Register from './components/Register';

// Pages
import AddNote from './pages/AddNote';
import EditNote from './pages/EditNote';
 
export const routes = [
    {
        name: 'login',
        path: '/login',
        component: Login
    },
    {
        name: 'register',
        path: '/register',
        component: Register
    },
    {
        name: 'notes',
        path: '/',
        component: Notes
    },
    {
        name: 'addNote',
        path: '/add-note',
        component: AddNote
    },
    {
        name: 'editNote',
        path: '/edit-note/:id',
        component: EditNote
    },
];
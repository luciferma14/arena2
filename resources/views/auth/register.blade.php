@extends('layouts.app')

@section('title', 'Crear Cuenta - TicketLand')

@section('content')
<div class="max-w-2xl mx-auto py-12">
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-xl p-10 shadow-2xl">
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black text-white mb-2">Únete a TicketLand</h1>
            <p class="text-slate-400">Crea tu cuenta en segundos</p>
        </div>

        <form id="signupForm" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 text-sm font-semibold mb-1.5">Nombre</label>
                    <input type="text" id="fname" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-slate-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                    <div id="fnameErr" class="text-xs text-red-400 mt-1 hidden"></div>
                </div>

                <div>
                    <label class="block text-slate-300 text-sm font-semibold mb-1.5">Apellido</label>
                    <input type="text" id="lname" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-slate-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                    <div id="lnameErr" class="text-xs text-red-400 mt-1 hidden"></div>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 text-sm font-semibold mb-1.5">Email</label>
                <input type="email" id="emailAddr" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-slate-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                <div id="emailErr" class="text-xs text-red-400 mt-1 hidden"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 text-sm font-semibold mb-1.5">Contraseña</label>
                    <input type="password" id="passwd" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-slate-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required minlength="8">
                    <div id="passwdErr" class="text-xs text-red-400 mt-1 hidden"></div>
                    <p class="text-xs text-slate-400 mt-1">Mín. 8 caracteres</p>
                </div>

                <div>
                    <label class="block text-slate-300 text-sm font-semibold mb-1.5">Confirmar</label>
                    <input type="password" id="passwdConf" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-slate-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none" required>
                    <div id="passwdConfErr" class="text-xs text-red-400 mt-1 hidden"></div>
                </div>
            </div>

            <button type="submit" class="w-full py-3 mt-6 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition">
                Crear Cuenta
            </button>

            <div class="text-center text-slate-400 text-sm mt-6">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300">Accede aquí</a>
            </div>
        </form>
    </div>
</div>

<script>
const SignUpCtrl = {
    async init() {
        document.getElementById('signupForm').addEventListener('submit', (e) => this.submit(e));
    },

    clearErrors() {
        ['fnameErr', 'lnameErr', 'emailErr', 'passwdErr', 'passwdConfErr'].forEach(id => {
            document.getElementById(id).classList.add('hidden');
        });
    },

    showError(fieldId, msg) {
        document.getElementById(fieldId).textContent = msg;
        document.getElementById(fieldId).classList.remove('hidden');
    },

    async submit(e) {
        e.preventDefault();
        this.clearErrors();

        const pwd = document.getElementById('passwd').value;
        const pwdConf = document.getElementById('passwdConf').value;

        if (pwd !== pwdConf) {
            this.showError('passwdConfErr', '❌ Las contraseñas no coinciden');
            return;
        }

        try {
            const { data } = await window.axios.post('/register', {
                nombre: document.getElementById('fname').value,
                apellido: document.getElementById('lname').value,
                email: document.getElementById('emailAddr').value,
                password: pwd,
                password_confirmation: pwdConf
            });

            Auth.set(data.token);
            location.href = '{{ route("dashboard") }}';
        } catch (err) {
            const msg = err.response?.data?.message || 'Error al registrarse';
            this.showError('emailErr', '❌ ' + msg);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => SignUpCtrl.init());
</script>
@endsection
            this.showError('email', '❌ Error de conexión');
        }
    }
};

SignUpCtrl.form.addEventListener('submit', e => SignUpCtrl.submit(e));
</script>
@endsection

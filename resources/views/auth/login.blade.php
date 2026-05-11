@extends('layouts.app')

@section('title', 'Acceso - TicketLand')

@section('content')
<div class="grid md:grid-cols-2 gap-8 items-center min-h-[calc(100vh-200px)] py-12">
    <div class="space-y-6">
        <div>
            <h1 class="text-4xl font-black text-white mb-2">Bienvenido</h1>
            <p class="text-slate-400">Accede a tu cuenta para comprar entradas</p>
        </div>
        <div class="space-y-4 text-slate-300">
            <div class="flex gap-3"><span class="text-indigo-400">→</span><span>Explora eventos en vivo</span></div>
            <div class="flex gap-3"><span class="text-indigo-400">→</span><span>Reserva tus asientos</span></div>
            <div class="flex gap-3"><span class="text-indigo-400">→</span><span>Acceso inmediato</span></div>
        </div>
    </div>

    <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-8 backdrop-blur">
        <form id="authForm" class="space-y-5">
            <div>
                <label class="block text-slate-300 text-sm font-semibold mb-2">Email</label>
                <input type="email" id="emailField" placeholder="tu@email.com" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                <div id="emailError" class="text-xs text-red-400 mt-1 hidden"></div>
            </div>

            <div>
                <label class="block text-slate-300 text-sm font-semibold mb-2">Contraseña</label>
                <input type="password" id="pwdField" placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                <div id="pwdError" class="text-xs text-red-400 mt-1 hidden"></div>
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition">
                Acceder
            </button>

            <div class="text-center text-slate-400 text-sm">
                ¿Sin cuenta? <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300">Crea una aquí</a>
            </div>
        </form>
    </div>
</div>

<script>
const LoginCtrl = {
    async init() {
        document.getElementById('authForm').addEventListener('submit', (e) => this.submit(e));
    },

    async submit(e) {
        e.preventDefault();
        document.getElementById('emailError').classList.add('hidden');
        document.getElementById('pwdError').classList.add('hidden');

        try {
            const { data } = await window.axios.post('/login', {
                email: document.getElementById('emailField').value,
                password: document.getElementById('pwdField').value
            });

            Auth.set(data.token);
            location.href = '{{ route("dashboard") }}';
        } catch(err) {
            const msg = err.response?.data?.message || 'Error de conexión';
            document.getElementById('emailError').textContent = '❌ ' + msg;
            document.getElementById('emailError').classList.remove('hidden');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => LoginCtrl.init());
</script>
@endsection
                document.getElementById('emailError').classList.remove('hidden');
            }
        } catch (err) {
            document.getElementById('pwdError').textContent = '❌ Error de conexión';
            document.getElementById('pwdError').classList.remove('hidden');
        }
    }
};

LoginCtrl.form.addEventListener('submit', e => LoginCtrl.submit(e));
</script>
@endsection

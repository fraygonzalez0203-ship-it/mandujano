<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Dental</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <header class="bg-white border-b border-slate-200">
        <nav class="max-w-6xl mx-auto px-6 py-5 flex justify-between items-center">
            <a href="{{ route('landing') }}" class="text-xl font-bold text-teal-700">Clínica Dental</a>
            <a href="{{ route('login') }}" class="rounded-lg bg-teal-700 px-5 py-2.5 text-white hover:bg-teal-800">Acceso del dentista</a>
        </nav>
    </header>

    <main>
        <section class="max-w-6xl mx-auto px-6 py-24 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="uppercase tracking-widest text-sm font-semibold text-teal-700 mb-4">Tu sonrisa es nuestra prioridad</p>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">Cuidamos tu sonrisa con atención personalizada.</h1>
                <p class="text-lg text-slate-600 mb-8">Atención dental profesional en un ambiente cómodo y confiable.</p>
                <a href="{{ route('login') }}" class="inline-block rounded-lg bg-teal-700 px-6 py-3 text-white font-semibold hover:bg-teal-800">Acceso del dentista</a>
            </div>
            <div class="rounded-3xl bg-teal-100 p-12 text-center">
                <div class="text-8xl mb-5">🦷</div>
                <h2 class="text-2xl font-bold text-teal-900">Atención dental</h2>
                <p class="mt-3 text-teal-800">Agenda y seguimiento de citas en un solo lugar.</p>
            </div>
        </section>

        <section class="bg-white py-16">
            <div class="max-w-6xl mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-10">Nuestros servicios</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ([
                        ['Consulta general', 'Evaluación de tu salud bucal.'],
                        ['Limpieza dental', 'Prevención y cuidado de tus dientes.'],
                        ['Resina dental', 'Tratamiento para reparar piezas dentales.'],
                        ['Extracción dental', 'Atención para extracciones necesarias.'],
                        ['Blanqueamiento dental', 'Tratamiento estético para tu sonrisa.']
                    ] as $servicio)
                        <article class="rounded-2xl border border-slate-200 p-6">
                            <div class="text-3xl mb-4">✨</div>
                            <h3 class="text-xl font-semibold mb-2">{{ $servicio[0] }}</h3>
                            <p class="text-slate-600">{{ $servicio[1] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-6 py-16 text-center">
            <h2 class="text-2xl font-bold mb-3">Horario de atención</h2>
            <p class="text-slate-600">Lunes a viernes, de 9:00 a 18:00 horas.</p>
        </section>
    </main>

    <footer class="bg-slate-900 text-slate-300 py-6 text-center">
        <p>Clínica Dental · Atención y cuidado de tu sonrisa</p>
    </footer>
</body>
</html>

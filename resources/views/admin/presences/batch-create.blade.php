<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer des créneaux en batch - Paul Hand Wash</title>
    <style>
        .date-selector { margin: 10px 0; }
        .date-list { display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; }
        .date-tag { background: #e3f2fd; padding: 5px 10px; border-radius: 15px; }
        .time-input { width: 80px; }
    </style>
</head>
<body>
    <h1>Créer des créneaux en batch</h1>

    <form action="{{ route('admin.presences.batch') }}" method="POST">
        @csrf

        <div>
            <label for="lieu_id">Lieu *</label>
            <select name="lieu_id" id="lieu_id" required>
                <option value="">Sélectionner un lieu</option>
                @foreach($lieux as $lieu)
                    <option value="{{ $lieu->id }}">{{ $lieu->nom }}</option>
                @endforeach
            </select>
            @error('lieu_id')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div class="date-selector">
            <label>Dates *</label>
            <div>
                <input type="date" id="date-picker" onchange="addDate(this.value)">
                <small>Sélectionnez plusieurs dates</small>
            </div>
            <div id="selected-dates" class="date-list"></div>
            <div id="hidden-dates"></div>
            @error('dates')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="heure_debut">Heure de début *</label>
            <input type="time" name="heure_debut" id="heure_debut" value="09:00" class="time-input" required>
            @error('heure_debut')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="heure_fin">Heure de fin *</label>
            <input type="time" name="heure_fin" id="heure_fin" value="19:00" class="time-input" required>
            @error('heure_fin')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-top: 20px;">
            <button type="submit">Créer les créneaux</button>
            <a href="{{ route('admin.presences.index') }}">Annuler</a>
        </div>
    </form>

    <script>
        let selectedDates = [];

        function addDate(date) {
            if (date && !selectedDates.includes(date)) {
                selectedDates.push(date);
                selectedDates.sort();
                updateDisplay();
            }
            document.getElementById('date-picker').value = '';
        }

        function removeDate(date) {
            selectedDates = selectedDates.filter(d => d !== date);
            updateDisplay();
        }

        function updateDisplay() {
            // Mettre à jour l'affichage visuel
            const container = document.getElementById('selected-dates');
            container.innerHTML = selectedDates.map(date => 
                `<span class="date-tag">${date} <button type="button" onclick="removeDate('${date}')">×</button></span>`
            ).join('');

            // Mettre à jour les inputs cachés
            const hiddenContainer = document.getElementById('hidden-dates');
            hiddenContainer.innerHTML = selectedDates.map(date => 
                `<input type="hidden" name="dates[]" value="${date}">`
            ).join('');
        }
    </script>
</body>
</html>

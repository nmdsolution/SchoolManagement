    <style>
        .observation-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        .observation-table th, .observation-table td {
            border: 1px solid black;
            text-align: center;
            padding: 3px;
        }
        .observation-table th {
            background-color: #f2f2f2;
        }
        .observation-table .section-title {
            font-weight: bold;
            text-align: left;
        }
        .observation-table .signatures {
            height: 10px;
        }
        .observation-table .observation, .avis {
            height: 100px;
            text-align: center;
        }
    </style>

<div style="page-break-before: always;">
    <table class="observation-table">
        <thead>
            <tr>
                <th colspan="4">OBSERVATIONS GÉNÉRALES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($observations as $observation)
            <tr>
                <!-- Observation par trimestre -->
                <td rowspan="2" class="observation">Observation {{ $observation->exam_term->name }}</td>
                <td colspan="3">SIGNATURES</td>
            </tr>
            <tr>
                <td>Enseignant(e)</td>
                <td>Directeur de l'ecole</td>
                <td>Parents</td>
            </tr>
            <tr style="min-height: 50px;">
                <td>
                    {{ $observation->observation }}
                </td>
                <td>
                    @if ($observation->teacher_signature)
                    <img src="{{ asset('storage/' . $observation->teacher_signature) }}" width="60px" height="100px" alt="Signature Enseignant(e)">
                    @else
                    Non signé
                    @endif
                </td>
                <td>
                    @if ($observation->director_signature)
                    <img src="{{ asset('storage/' . $observation->director_signature) }}" width="60px" height="100px" alt="Signature Directeur">
                    @else
                    Non signé
                    @endif
                </td>
                <td>
                    @if ($observation->parent_signature)
                    <img src="{{ asset('storage/' . $observation->parent_signature) }}" width="60px" height="100px" alt="Signature Parent">
                    @else
                    Non signé
                    @endif
                </td>
            </tr>
            @endforeach
            <!-- Avis du Conseil des Maîtres -->
            <tr>
                <td colspan="4"><strong>AVIS DU CONSEIL DES MAÎTRES</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="avis">
                    @if ($councilReview)
                    {{ $councilReview->review }}
                    @else
                    Aucun avis enregistré.
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
{{-- 
    <table>
        <thead>
            <tr>
                <th colspan="4">OBSERVATIONS GÉNÉRALES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="2" class="section-title">Observation 1er trimestre</td>
                <td colspan="3" class="signatures">SIGNATURES</td>
            </tr>
            <tr>
                <td>Enseignant(e)</td>
                <td>Directeur de l’école</td>
                <td>Parents</td>
            </tr>
            <tr class="observation">
                <td colspan="4"></td>
            </tr>
            <tr>
                <td rowspan="2" class="section-title">Observation 2ème trimestre</td>
                <td colspan="3" class="signatures">SIGNATURES</td>
            </tr>
            <tr>
                <td>Enseignant(e)</td>
                <td>Directeur de l’école</td>
                <td>Parents</td>
            </tr>
            <tr class="observation">
                <td colspan="4"></td>
            </tr>
            <tr>
                <td rowspan="2" class="section-title">Observation 3ème trimestre</td>
                <td colspan="3" class="signatures">SIGNATURES</td>
            </tr>
            <tr>
                <td>Enseignant(e)</td>
                <td>Directeur de l’école</td>
                <td>Parents</td>
            </tr>
            <tr class="observation">
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="4" class="section-title">AVIS DU CONSEIL DES MAÎTRES</td>
            </tr>
            <tr class="avis">
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table> --}}
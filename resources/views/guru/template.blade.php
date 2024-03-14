<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Leave a comment here" name="data[soal][1][isi_soal][]" id="editor"></textarea>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <input type="text" name="data[soal][1][isi_jawaban][]" class="form-control bg-white" placeholder="Pilihan A">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <input type="text" name="data[soal][1][isi_jawaban][]" class="form-control bg-white" placeholder="Pilihan B">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <input type="text" name="data[soal][1][isi_jawaban][]" class="form-control bg-white" placeholder="Pilihan C">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <input type="text" name="data[soal][1][isi_jawaban][]" class="form-control bg-white" placeholder="Pilihan D">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <input type="text" name="data[soal][1][isi_jawaban][]" class="form-control bg-white" placeholder="Pilihan E">
        </div>
    </div>
</div>
<select class="form-select bg-white" name="data[soal][1][isi_jawaban_correct]" aria-label="Default select example">
    <option selected>Pilih Jawaban</option>
    <option value="0">A</option>
    <option value="1">B</option>
    <option value="2">C</option>
    <option value="3">D</option>
    <option value="4">E</option>
</select>
<script>
$(document).ready(function(){


function initalize(){
    return $.ajax({
        url: 'http://127.0.0.1:8000/guru/template',
        method: 'GET',
        success: function(response){
            return response
        },
        error: function(jqXHR){
            console.log(jqXHR)
        }
    })
}

$.when(initalize()).done(function(data){
    if(data.ok){
        for(let i = 0; i < data.data.length; i++){
            $("#lists").append(
                `
                <tr>
                    <th scope="row">${i + 1}</th>
                    <td>${data.data[i].information.user_name}</td>
                    <td>${data.data[i].information.jumlah_soal}</td>
                    <td>${data.data[i].information.total_benar}</td>
                    <td>${data.data[i].information.total_salah}</td>
                    <td>${data.data[i].information.total_presentase}</td>
                    <td>${data.data[i].information.mapel}</td>
                    <td>X</td>
                </tr>
                `
            )
        }
    }
})

})
</script>
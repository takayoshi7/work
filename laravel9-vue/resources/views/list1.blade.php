<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('社員一覧') }}
        </h2>
    </x-slot>

    <div id="app2">
    </div>


    {{-- data:image/png;base64,をつけてあげることによって、base64でエンコードされた画像を表示するという記法になる --}}
    {{-- <img class="listmyimg" src="data:image/png;base64,{{ $member->img1 }}" width="30px">
    <img class="listmyimg" src="{{ \Storage::url('img/no_image.jpg') }}" width="30px">


{{-- CSVインポートダイアログ --}}
{{-- <div id="app2">
    <p>インポートしたいCSVファイルを選択し、インポートするボタンを押してください。</p>
    <p>全ての項目に入力が必須になります。</p><br>
    <P>ユーザＩＤ ＝ id：半角の数字とアルファベットの組み合わせ。重複禁止。</p>
    <P>社員コード ＝ empno：半角数字4桁のみ。重複禁止。</p>
    <P>社員名 ＝ ename：文字列のみ（半角・全角）</p>
    <P>職種 ＝ mgr：文字列のみ（半角・全角）</p>
    <P>入社日 ＝ hiredate：西暦で記入。斜線で区切る（例. 1999/1/11）</p>
    <P>給与 ＝ sal：半角数字のみ</p>
    <P>歩合 ＝ comm：半角数字のみ</p>
    <P>部署コード ＝ deptno：登録してある部署コードを入力（半角数字2桁）</p>
    <P>役割 ＝ role：半角数字のみ。次から選択（1：管理者 ・ 2：役員 ・ 3：人事）</p><br>
    <form method="POST" id="my_form2">
    @csrf
    <input type="file" id="csv_file" name="csv_file" class="form-control">
    <br><br>
    <button type="button" id="import1" class="originalhidden">インポートする</button>
    <div id="error"></div>
    </form>
</div> --}}

{{-- 画像1追加ダイアログ --}}
{{-- <div id="select_img1">
    <p>変更したい画像ファイルを選択し、</p>
    <p>アップロードボタンを押してください。</p><br>
    <P>対応拡張子(.jpg　.gif　.png)</p>
    <form method="POST" enctype="multipart/form-data"  id="img1_form">
    @csrf
    <input type="hidden" id="dialog_empno">
    <input type="file" accept="image/*" id="simg1" name="simg1" class="form-control">
    <br><br>
    <button type="button" id="inimg1" class="originalhidden">アップロード</button>
    <br>
    <div id="img1error"></div>
    </form>
</div> --}}

{{-- 画像2追加ダイアログ --}}
{{-- <div id="select_img2">
    <p>変更したい画像ファイルを選択し、</p>
    <p>アップロードボタンを押してください。</p><br>
    <P>対応拡張子(.jpg　.gif　.png)</p>
    <form method="POST" enctype="multipart/form-data"  id="img2_form">
    @csrf
    <input type="hidden" id="dialog_empno">
    <input type="file" accept="image/*" id="simg2" name="simg2" class="form-control">
    <br>
    <button type="button" id="inimg2" class="originalhidden">アップロード</button>
    <br>
    <div id="img2error"></div>
    </form>
</div> --}}

</x-app-layout>

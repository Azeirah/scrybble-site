<div {{ $attributes }}>
    <h1>Get started</h1>
    <p>Provide your ReMarkable <i>one-time-code</i> below</p>
    <a target="_blank"
       href="https://my.remarkable.com/device/desktop/connect">Get one-time-code</a>
    <form method="POST" action="/onetimecode">
        @csrf
        <div class="input-group">
            <input class="input-group-text" required minlength=8 maxlength=8 pattern="[a-z]{8}"
                   placeholder="aabbccdd" name="code" type="text">
            <input class="btn btn-primary" type="submit" value="submit">
        </div>
    </form>
</div>

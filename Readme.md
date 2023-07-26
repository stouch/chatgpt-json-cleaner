# Some regexes to fix the broken JSON produced by ChatGPT

**(frequent behaviour for high `frequency_penalty`)**

Even using that kind of prompt : https://community.openai.com/t/getting-response-data-as-a-fixed-consistent-json-response/28471/4

```
const prompt = `
pretend to be an expert child behavioural researcher.
create a valid JSON array of objects for translating baby speak into English following this format:

[{"baby": "sound the baby makes",
"volumeDb": "how loud is the sound, decibels as a floating-point number",
"timeMin": "how long the sound is made, minutes with 2 decimal places",
"meaning": "what the baby might be trying to communicate",
"confidencePct": "certainty of meaning, percent as an integer,
"response": "what sound the parent should reply with"}]

The JSON object:
`.trim()
```

... ChatGPT sometimes gives me really ugly JSON.

I typically got that kind of results (but many other cases) :

![Screenshot from 2023-02-26 22-32-37](https://user-images.githubusercontent.com/17531455/221438616-0503e670-b62c-4984-9a68-8d6378b46b18.png)

This behaviour is frequent in case you use a high `frequency_penalty` in your request.

So I tried these regexes to improve it. Let me know what you use ! Thanks a lot.

## Tests

Fill test.json.file

```bash
php test.php
```

If you want to debug a specific string, set your own string in `$content` at the top of `test.php`
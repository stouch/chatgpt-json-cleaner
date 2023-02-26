# Some regexes to improve the JSON produced by ChatGPT

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

So I tried these regexes to improve it. Let me know what you use ! Thanks a lot.

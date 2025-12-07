// netlify/functions/generate.js
const { MODELS } = require("./_models");

const HF_API_KEY = process.env.HF_API_KEY;

async function callHuggingFace(model, prompt) {
  if (!HF_API_KEY) {
    throw new Error("Missing HF_API_KEY environment variable");
  }

  const max_tokens = model.settings?.max_new_tokens ?? 256;
  const temperature = model.settings?.temperature ?? 0.7;

  // ✅ New, correct Router endpoint (OpenAI-style)
  const res = await fetch("https://router.huggingface.co/v1/chat/completions", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${HF_API_KEY}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      model: model.modelName, // e.g. "google/gemma-2-2b-it"
      messages: [
        {
          role: "user",
          content: prompt,
        },
      ],
      max_tokens,
      temperature,
    }),
  });

  if (!res.ok) {
    const text = await res.text();
    throw new Error(`HF error (${model.modelName}): ${res.status} - ${text}`);
  }

  const data = await res.json();

  // OpenAI-style response: choices[0].message.content
  const content =
    data.choices?.[0]?.message?.content || JSON.stringify(data, null, 2);

  return content;
}

exports.handler = async (event) => {
  if (event.httpMethod === "OPTIONS") {
    return {
      statusCode: 204,
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Methods": "POST,OPTIONS",
        "Access-Control-Allow-Headers": "Content-Type",
      },
      body: "",
    };
  }

  if (event.httpMethod !== "POST") {
    return {
      statusCode: 405,
      body: JSON.stringify({ error: "Method not allowed" }),
    };
  }

  try {
    const body = JSON.parse(event.body || "{}");
    const prompt = body.prompt;

    if (!prompt) {
      return {
        statusCode: 400,
        body: JSON.stringify({ error: "Missing prompt" }),
      };
    }

    const promises = MODELS.map(async (model) => {
      if (model.provider === "huggingface") {
        const output = await callHuggingFace(model, prompt);
        return { id: model.id, label: model.label, output };
      }
      return {
        id: model.id,
        label: model.label,
        output: "Provider not implemented",
      };
    });

    const results = await Promise.all(promises);

    return {
      statusCode: 200,
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "*",
      },
      body: JSON.stringify({ results }),
    };
  } catch (err) {
    console.error(err);
    return {
      statusCode: 500,
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "*",
      },
      body: JSON.stringify({ error: err.message }),
    };
  }
};

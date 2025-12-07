// List of models you want to compare side by side
// You can change labels or models later.

const MODELS = [
  {
    id: "mistral",
    label: "Mistral 7B Instruct",
    provider: "huggingface",
    modelName: "mistralai/Mistral-7B-Instruct-v0.3",
    description: "A strong general-purpose model for reasoning and writing.",
    settings: {
      max_new_tokens: 256,
      temperature: 0.7,
    },
  },
  {
    id: "gemma",
    label: "Gemma 2B Instruct",
    provider: "huggingface",
    modelName: "google/gemma-2-2b-it",
    description: "Smaller but efficient model for chat & coding.",
    settings: {
      max_new_tokens: 256,
      temperature: 0.6,
    },
  },
  {
    id: "phi",
    label: "Phi-3 Mini 4K Instruct",
    provider: "huggingface",
    // FIXED CASE SENSITIVE NAME 🚀
    modelName: "microsoft/Phi-3-mini-4k-instruct",
    description: "Very strong small model with clean outputs.",
    settings: {
      max_new_tokens: 256,
      temperature: 0.65,
    },
  },
];

module.exports = { MODELS };

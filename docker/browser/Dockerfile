FROM node:22.13.1-bookworm

RUN npm install --global --no-audit --no-fund pnpm@11.17.0 \
    && npx --yes @playwright/test@1.54.1 install-deps chromium

WORKDIR /app

FROM node:22.14.0-alpine

WORKDIR /app

COPY package.json .
COPY package-lock.json .

RUN npm install --ignore-scripts

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
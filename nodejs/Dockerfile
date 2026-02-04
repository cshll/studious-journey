FROM node:18-slim
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
COPY start.sh .
RUN chmod +x start.sh
CMD ["./start.sh"]
EXPOSE 3000

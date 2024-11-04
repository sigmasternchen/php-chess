FROM node:22 AS js-build

WORKDIR /app
RUN mkdir -p /html/static/

COPY js/package*.json ./
RUN npm install

COPY js/src ./src
RUN npm run build

FROM php:8.1-apache

WORKDIR /var/www/
COPY . .
RUN rm -rf js/ .github/ tests/ .git/

COPY --from=js-build /html/static/bundle.js ./html/static/

EXPOSE 80
CMD ["apache2-foreground"]

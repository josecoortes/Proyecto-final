output "front_public_ip" {
  description = "IP pública del Frontend (Copia esto en GitHub Secrets: EC2_FRONTEND_HOST)"
  value       = aws_eip.front_burguermarina_eip.public_ip
}

output "back_public_ip" {
  description = "IP pública del BackEnd (Copia esto en GitHub Secrets: EC2_BACKEND_HOST)"
  value       = aws_eip.back_burguermarina_eip.public_ip
}

output "internal_api_url" {
  description = "URL interna que ya he configurado en tu Nginx"
  value       = "http://${var.back_burguermarina_name}:8000/api"
}

terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 6.0"
    }
  }
  required_version = ">= 1.2"
}

provider "aws" {
  region = var.region
}

#     GRUPOS DE SEGURIDAD       #

resource "aws_security_group" "allow_ssh_burguermarina" {
  name        = "Allow SSH burguerMarina"
  description = "Grupo de seguridad para permitir la conexion por ssh"
}
resource "aws_vpc_security_group_ingress_rule" "allow_ssh_burguermarina" {
  security_group_id = aws_security_group.allow_ssh_burguermarina.id
  from_port         = 22
  to_port           = 22
  ip_protocol       = "tcp"
  cidr_ipv4         = "0.0.0.0/0"
}

resource "aws_security_group" "allow_all_burguermarina" {
  name        = "Allow ALL burguerMarina Salida"
  description = "Grupo de seguridad para abrir los puertos de salida"
}
resource "aws_vpc_security_group_egress_rule" "allow_all_burguermarina" {
  security_group_id = aws_security_group.allow_all_burguermarina.id
  cidr_ipv4         = "0.0.0.0/0"
  ip_protocol       = "-1"
}

# --- FRONTEND SG ---
resource "aws_security_group" "front_group_burguermarina" {
  name        = "Front Group burguerMarina"
  description = "Grupo de seguridad para la instancia de front"
}
resource "aws_vpc_security_group_ingress_rule" "allow_http_front_burguermarina" {
  security_group_id = aws_security_group.front_group_burguermarina.id
  from_port         = 80
  to_port           = 80
  ip_protocol       = "tcp"
  cidr_ipv4         = "0.0.0.0/0"
}
resource "aws_vpc_security_group_ingress_rule" "allow_https_front_burguermarina" {
  security_group_id = aws_security_group.front_group_burguermarina.id
  from_port         = 443
  to_port           = 443
  ip_protocol       = "tcp"
  cidr_ipv4         = "0.0.0.0/0"
}

# --- BACKEND SG ---
resource "aws_security_group" "back_group_burguermarina" {
  name        = "Back group burguerMarina"
  description = "Grupo de seguridad para la instancia de back"
}
resource "aws_vpc_security_group_ingress_rule" "allow_8000_back" {
  security_group_id            = aws_security_group.back_group_burguermarina.id
  from_port                    = 8000
  to_port                      = 8000
  ip_protocol                  = "tcp"
  referenced_security_group_id = aws_security_group.front_group_burguermarina.id
}
resource "aws_vpc_security_group_ingress_rule" "allow_8000_back_directo" {
  security_group_id = aws_security_group.back_group_burguermarina.id
  from_port         = 8000
  to_port           = 8000
  ip_protocol       = "tcp"
  cidr_ipv4         = "0.0.0.0/0"
}

#     CREACIÓN DE INSTANCIAS    #

resource "aws_instance" "front_burguermarina" {
  instance_type = var.instance_type
  key_name      = var.key_name
  ami           = data.aws_ami.ubuntu.id

  vpc_security_group_ids = [
    aws_security_group.allow_all_burguermarina.id,
    aws_security_group.allow_ssh_burguermarina.id,
    aws_security_group.front_group_burguermarina.id,
  ]

  root_block_device {
    volume_size = 20
    volume_type = "gp3"
  }

  tags = {
    Name = "Front-burguerMarina"
  }

  user_data_replace_on_change = true
  user_data                   = file("${path.module}/scripts/userdata.sh")
}

resource "aws_eip" "front_burguermarina_eip" {
  instance = aws_instance.front_burguermarina.id
  domain   = "vpc"
  tags = {
    Name = "Front-burguerMarina-eip"
  }
}

resource "aws_instance" "back_burguermarina" {
  instance_type = var.instance_type
  key_name      = var.key_name
  ami           = data.aws_ami.ubuntu.id

  vpc_security_group_ids = [
    aws_security_group.allow_all_burguermarina.id,
    aws_security_group.allow_ssh_burguermarina.id,
    aws_security_group.back_group_burguermarina.id,
  ]

  root_block_device {
    volume_size = 20
    volume_type = "gp3"
  }

  tags = {
    Name = "Back-burguerMarina"
  }

  user_data_replace_on_change = true
  user_data                   = file("${path.module}/scripts/userdata.sh")
}

resource "aws_eip" "back_burguermarina_eip" {
  instance = aws_instance.back_burguermarina.id
  domain   = "vpc"
  tags = {
    Name = "Back-burguerMarina-eip"
  }
}


#            ROUTE 53           #

resource "aws_route53_zone" "internal_burguermarina" {
  name = "burguermarina.internal"
  vpc {
    vpc_id = data.aws_vpc.default.id
  }
}

resource "aws_route53_record" "internal_back_burguermarina" {
  zone_id = aws_route53_zone.internal_burguermarina.zone_id
  name    = var.back_burguermarina_name
  type    = "A"
  ttl     = "300"
  records = [aws_instance.back_burguermarina.private_ip]
}

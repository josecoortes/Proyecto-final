variable "region" {
    type = string
    default = "us-east-1"
}

variable "ubuntu_version" {
    type = string
    default = "jammy-22.04"
}

# CAMBIA ESTO por el nombre de tu clave SSH actual en AWS sin el .pem
variable "key_name"{
    type = string
    default = "vockey" 
}

variable "instance_type" {
    type = string
    default = "t3.small"
}

variable "back_burguermarina_name" {
    type = string
    default = "api.burguermarina.internal"
}

data "aws_ami" "ubuntu" {
    most_recent = true
    filter {
        name = "name"
        values = ["ubuntu/images/hvm-ssd/ubuntu-${var.ubuntu_version}-amd64-server-*"]
    }
    filter{
        name = "virtualization-type"
        values = ["hvm"]
    }
    owners = ["099720109477"]
}

data "aws_vpc" "default" {
    default = true
    region = var.region
}

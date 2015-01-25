package br.com.guidebar.bean;

public class Usuario {
	private Integer id;
	private String nome;
	private String email;
	private String senha;
	private String dataNascimento;
	private Integer gender;
	private Float reputacaoMedia;
	private Boolean isConfirmado;
	private String idFacebook;
	private String thumbnail;
	private String tokenPagSeguro;
	private String emailPagSeguro;
	private String accessToken;
	

	public Usuario() {
	}

	public Usuario(String nome) {
		this.nome = nome;
	}

	public Usuario(int id) {
		this.id = id;
	}

	@Override
	public String toString() {
		return getNome();
	}

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getNome() {
		return nome;
	}

	public void setNome(String nome) {
		this.nome = nome;
	}

	public String getEmail() {
		return email;
	}

	public void setEmail(String email) {
		this.email = email;
	}

	public String getSenha() {
		return senha;
	}

	public void setSenha(String senha) {
		this.senha = senha;
	}

	public String getDataNascimento() {
		return dataNascimento;
	}

	public void setDataNascimento(String dataNascimento) {
		this.dataNascimento = dataNascimento;
	}

	public Integer getGender() {
		return gender;
	}

	public void setGender(Integer gender) {
		this.gender = gender;
	}

	public Float getReputacaoMedia() {
		return reputacaoMedia;
	}

	public void setReputacaoMedia(Float reputacaoMedia) {
		this.reputacaoMedia = reputacaoMedia;
	}

	public Boolean getIsConfirmado() {
		return isConfirmado;
	}

	public void setIsConfirmado(Boolean isConfirmado) {
		this.isConfirmado = isConfirmado;
	}

	public String getThumbnail() {
		return thumbnail;
	}

	public void setThumbnail(String thumbnail) {
		this.thumbnail = thumbnail;
	}

	public String getIdFacebook() {
		return idFacebook;
	}

	public void setIdFacebook(String idFacebook) {
		this.idFacebook = idFacebook;
	}

	public String getTokenPagSeguro() {
		return tokenPagSeguro;
	}

	public void setTokenPagSeguro(String tokenPagSeguro) {
		this.tokenPagSeguro = tokenPagSeguro;
	}

	public String getEmailPagSeguro() {
		return emailPagSeguro;
	}

	public void setEmailPagSeguro(String emailPagSeguro) {
		this.emailPagSeguro = emailPagSeguro;
	}

	public String getAccessToken() {
		return accessToken;
	}

	public void setAccessToken(String accessToken) {
		this.accessToken = accessToken;
	}

}
